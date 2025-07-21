<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\YogurtProduct;
use App\Models\OrderItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\OrderProcessingService;

class CustomerOrderController extends Controller
{
    protected $orderProcessingService;

    public function __construct(OrderProcessingService $orderProcessingService)
    {
        $this->orderProcessingService = $orderProcessingService;
    }

    public function index()
    {
        $orders = Auth::user()->orders()->latest()->get();
        $ordersByDate = $orders->groupBy(function($order) {
            return optional($order->order_date)->format('Y-m-d');
        });
        return view('customer.orders.index', compact('ordersByDate'));
    }

    public function show($id)
    {
        $order = Auth::user()->orders()->findOrFail($id);
        return view('customer.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_contact' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'products' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'customer',
                'order_number' => 'ORD' . now()->format('YmdHis') . strtoupper(uniqid()),
                'order_date' => now(),
                'order_status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'delivery_contact' => $validated['delivery_contact'],
                'delivery_phone' => $validated['delivery_phone'],
            ]);

            $total = 0;
            foreach ($validated['products'] as $productData) {
                $quantity = intval($productData['quantity'] ?? 0);
                $productId = $productData['product_id'] ?? null;
                if ($quantity > 0 && $productId) {
                    $product = YogurtProduct::find($productId);
                    if ($product) {
                        $unitPrice = $product->selling_price;
                        $subtotal = $unitPrice * $quantity;
                        $total += $subtotal;
                        OrderItem::create([
                            'order_id' => $order->id,
                            'yogurt_product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $subtotal,
                            'final_price' => $subtotal,
                            'production_date' => now(),
                            'expiry_date' => now()->addDays(14),
                            'item_status' => 'pending',
                        ]);
                    }
                }
            }
            $order->total_amount = $total;
            $order->save();

            $order->refresh();
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            $eligibleVendors = Vendor::where('status', 'approved')->get()->filter(function($vendor) use ($orderItems) {
                foreach ($orderItems as $item) {
                    $product = $item->yogurtProduct;
                    if (!$product || $product->vendor_id != $vendor->id || $product->stock < $item->quantity) {
                        return false;
                    }
                }
                return true;
            });
            if ($eligibleVendors->isNotEmpty()) {
                $selectedVendor = $eligibleVendors->random();
                $order->vendor_id = $selectedVendor->id;
                $order->save();
                $selectedVendor->notify(new \App\Notifications\VendorAssignedOrderNotification($order));
            }

            $order->load('orderItems.yogurtProduct');

            $result = $this->orderProcessingService->processCustomerOrder($order);
            if (!$result) {
                DB::rollBack();
                return redirect()->route('cart.index')->with('error', 'Sorry, your order could not be placed due to insufficient inventory. Please try again.');
            }
            DB::commit();
            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
