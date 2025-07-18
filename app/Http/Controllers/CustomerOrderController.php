<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\OrderProcessingService;

class CustomerOrderController extends Controller
{
    protected $orderProcessingService;

    public function __construct(OrderProcessingService $orderProcessingService)
    {
        $this->orderProcessingService = $orderProcessingService;
    }

    // List all orders for the authenticated customer
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->get();
        // Group orders by order_date (date only)
        $ordersByDate = $orders->groupBy(function($order) {
            return optional($order->order_date)->format('Y-m-d');
        });
        return view('customer.orders.index', compact('ordersByDate'));
    }

    // Show a specific order for the authenticated customer
    public function show($id)
    {
        $order = Auth::user()->orders()->findOrFail($id);
        return view('customer.orders.show', compact('order'));
    }

    // Store a new order for the authenticated customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_contact' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'products' => 'required|array',
        ]);

        $order = null;
        \DB::beginTransaction();
        try {
            // Create the order
            $order = \App\Models\Order::create([
                'user_id' => \Auth::id(),
                'order_type' => 'customer',
                'order_number' => 'ORD' . now()->format('YmdHis') . strtoupper(uniqid()),
                'order_date' => now(),
                'order_status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'delivery_contact' => $validated['delivery_contact'],
                'delivery_phone' => $validated['delivery_phone'],
            ]);

            $total = 0;
            foreach ($request->input('products', []) as $productData) {
                $quantity = intval($productData['quantity'] ?? 0);
                $productId = $productData['product_id'] ?? null;
                if ($quantity > 0 && $productId) {
                    $product = \App\Models\YogurtProduct::find($productId);
                    if ($product) {
                        $unitPrice = $product->selling_price;
                        $subtotal = $unitPrice * $quantity;
                        $total += $subtotal;
                        \App\Models\OrderItem::create([
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

            // --- Automatic Vendor Assignment ---
            $order->refresh();
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            $eligibleVendors = \App\Models\Vendor::where('status', 'approved')->get()->filter(function($vendor) use ($orderItems) {
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
                // Notify the vendor
                $selectedVendor->notify(new \App\Notifications\VendorAssignedOrderNotification($order));
            } else {
                // Optionally: handle no eligible vendor (e.g., notify admin, mark as unassigned, etc.)
            }
            // --- End Automatic Vendor Assignment ---

            // Ensure order items are loaded before processing
            $order->load('orderItems.yogurtProduct');

            // Process the order (deduct inventory, confirm, assign distribution center)
            $result = $this->orderProcessingService->processCustomerOrder($order);
            if (!$result) {
                // Inventory deduction or confirmation failed
                \DB::rollBack();
                return redirect()->route('cart.index')->with('error', 'Sorry, your order could not be placed due to insufficient inventory. Please try again.');
            }
            \DB::commit();
            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Order creation failed', [
                'user_id' => \Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }


}
