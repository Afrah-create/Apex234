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
        $orders = Auth::user()->orders()->where('order_type', 'customer')->latest()->get();
        return view('customer.orders.index', compact('orders'));
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

        // Create the order
        $order = \App\Models\Order::create([
            'user_id' => \Auth::id(),
            'order_type' => 'customer',
            'order_number' => 'ORD' . now()->format('YmdHis') . strtoupper(uniqid()),
            'order_date' => now(),
            'order_status' => 'confirmed',
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

            // --- Send chat message to vendor with order details ---
            $customer = \Auth::user();
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            $productLines = $orderItems->map(function($item) {
                return $item->yogurtProduct->product_name . ' x ' . $item->quantity;
            })->implode("\n");
            $chatMsg = "New order from {$customer->name}:\n" .
                "Order #: {$order->order_number}\n" .
                "Phone: {$order->delivery_phone}\n" .
                "Address: {$order->delivery_address}\n" .
                "Contact: {$order->delivery_contact}\n" .
                "Products:\n{$productLines}\n" .
                "Total: {$order->total_amount}";
            
            \App\Models\ChatMessage::create([
                'sender_id' => $customer->id,
                'receiver_id' => $selectedVendor->user_id,
                'message' => $chatMsg,
                'is_read' => false
            ]);
            // --- End chat message ---
        } else {
            // Optionally: handle no eligible vendor (e.g., notify admin, mark as unassigned, etc.)
        }
        // --- End Automatic Vendor Assignment ---

        // --- Send chat message to all admins with order details ---
        $customer = \Auth::user();
        $orderItems = $order->orderItems()->with('yogurtProduct')->get();
        $productLines = $orderItems->map(function($item) {
            return $item->yogurtProduct->product_name . ' x ' . $item->quantity;
        })->implode("\n");
        $adminMsg = "New customer order from {$customer->name}:\n" .
            "Order #: {$order->order_number}\n" .
            "Phone: {$order->delivery_phone}\n" .
            "Address: {$order->delivery_address}\n" .
            "Contact: {$order->delivery_contact}\n" .
            "Products:\n{$productLines}\n" .
            "Total: {$order->total_amount}";
        $admins = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'admin'); })->get();
        foreach ($admins as $admin) {
            \App\Models\ChatMessage::create([
                'sender_id' => $customer->id,
                'receiver_id' => $admin->id,
                'message' => $adminMsg,
                'is_read' => false
            ]);
        }
        // --- End admin chat message ---

        // --- Send confirmation message to customer ---
        $confirmationMsg = "Thank you for your order! Your order #{$order->order_number} has been received and is being processed.";
        \App\Models\ChatMessage::create([
            'sender_id' => $admins->first()->id ?? 1, // Use first admin as sender, fallback to 1
            'receiver_id' => $customer->id,
            'message' => $confirmationMsg,
            'is_read' => false
        ]);
        // --- End confirmation message ---

        // Process the order (deduct inventory, confirm, assign distribution center)
        $this->orderProcessingService->processCustomerOrder($order);

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Order placed successfully!');
    }


}
