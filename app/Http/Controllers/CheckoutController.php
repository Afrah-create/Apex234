<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\YogurtProduct;
use App\Models\Retailer;
use App\Models\DistributionCenter;
use App\Services\OrderProcessingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        // Get cart items for authenticated user
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate inventory before checkout
        $inventoryIssues = [];
        foreach ($cartItems as $item) {
            $product = $item->product;
            if (!$product) {
                $inventoryIssues[] = "Product not found.";
                continue;
            }
            
            // Check if product has sufficient stock
            $availableStock = $product->stock ?? 0;
            if ($availableStock < $item->quantity) {
                $inventoryIssues[] = "Insufficient stock for {$product->product_name}. Available: {$availableStock}, Requested: {$item->quantity}";
            }
        }

        if (!empty($inventoryIssues)) {
            return redirect()->route('cart.index')->with('error', 'Inventory issues: ' . implode(', ', $inventoryIssues));
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->selling_price;
        });

        // Get available distribution centers
        $distributionCenters = DistributionCenter::where('status', 'operational')->get();

        return view('checkout.index', compact('cartItems', 'total', 'distributionCenters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_contact' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'distribution_center_id' => 'required|exists:distribution_centers,id',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer',
            'requested_delivery_date' => 'required|date|after:today',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        // Get cart items
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();
        try {
            // Final inventory validation
            $inventoryIssues = [];
            $total = 0;
            
            foreach ($cartItems as $item) {
                $product = $item->product;
                if (!$product) {
                    throw new \Exception("Product not found.");
                }
                
                $availableStock = $product->stock ?? 0;
                if ($availableStock < $item->quantity) {
                    $inventoryIssues[] = "Insufficient stock for {$product->product_name}. Available: {$availableStock}, Requested: {$item->quantity}";
                }
                
                $total += $item->quantity * $product->selling_price;
            }

            if (!empty($inventoryIssues)) {
                throw new \Exception('Inventory issues: ' . implode(', ', $inventoryIssues));
            }

            // Calculate additional costs
            $shippingCost = $this->calculateShippingCost($request->delivery_address, $request->distribution_center_id);
            $taxAmount = $this->calculateTax($total);
            $discountAmount = $this->calculateDiscount($total, Auth::user());
            $finalTotal = $total + $shippingCost + $taxAmount - $discountAmount;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'customer',
                'order_number' => 'CUST-' . now()->format('YmdHis') . strtoupper(uniqid()),
                'order_date' => now(),
                'order_status' => 'pending',
                'subtotal' => $total,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'total_amount' => $finalTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_contact' => $request->delivery_contact,
                'delivery_phone' => $request->delivery_phone,
                'special_instructions' => $request->special_instructions,
                'notes' => 'Customer order placed via checkout',
                'retailer_id' => null, // Customer orders don't have retailer
                'distribution_center_id' => $request->distribution_center_id,
                'requested_delivery_date' => $request->requested_delivery_date,
            ]);

            // Create order items and reserve inventory
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'yogurt_product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $product->selling_price,
                    'total_price' => $cartItem->quantity * $product->selling_price,
                    'discount_percentage' => 0,
                    'discount_amount' => 0,
                    'final_price' => $cartItem->quantity * $product->selling_price,
                    'production_date' => now(),
                    'expiry_date' => now()->addDays(14),
                    'item_status' => 'pending',
                    'notes' => null,
                ]);

                // Reserve inventory (deduct from available stock)
                $product->stock = max(0, $product->stock - $cartItem->quantity);
                $product->save();
            }

            // Clear cart
            CartItem::where('user_id', Auth::id())->delete();

            // Send order confirmation email
            $this->sendOrderConfirmationEmail($order);

            // Notify admin about new order
            $this->notifyAdminNewOrder($order);

            // Process the order automatically
            $orderProcessingService = new OrderProcessingService();
            $orderProcessingService->processCustomerOrder($order);

            DB::commit();

            Log::info('Customer order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => Auth::id(),
                'total_amount' => $finalTotal
            ]);

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully! Your order number is: ' . $order->order_number);

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

    /**
     * Calculate shipping cost based on delivery address and distribution center
     */
    private function calculateShippingCost($deliveryAddress, $distributionCenterId)
    {
        // Simple shipping calculation - can be enhanced with real distance calculation
        $baseShippingCost = 5000; // 5000 UGX base cost
        
        // Add distance-based cost (simplified)
        $distanceMultiplier = 1.0;
        if (str_contains(strtolower($deliveryAddress), 'kampala')) {
            $distanceMultiplier = 1.2;
        } elseif (str_contains(strtolower($deliveryAddress), 'jinja')) {
            $distanceMultiplier = 1.5;
        } elseif (str_contains(strtolower($deliveryAddress), 'mbarara')) {
            $distanceMultiplier = 2.0;
        }
        
        return round($baseShippingCost * $distanceMultiplier);
    }

    /**
     * Calculate tax amount
     */
    private function calculateTax($subtotal)
    {
        $taxRate = 0.18; // 18% VAT
        return round($subtotal * $taxRate);
    }

    /**
     * Calculate discount based on user loyalty or promotions
     */
    private function calculateDiscount($subtotal, $user)
    {
        $discount = 0;
        
        // Loyalty discount for returning customers
        $orderCount = Order::where('user_id', $user->id)
            ->where('order_status', 'delivered')
            ->count();
        
        if ($orderCount >= 5) {
            $discount += $subtotal * 0.05; // 5% loyalty discount
        }
        
        // First order discount
        if ($orderCount === 0) {
            $discount += $subtotal * 0.10; // 10% first order discount
        }
        
        return round($discount);
    }

    /**
     * Send order confirmation email to customer
     */
    private function sendOrderConfirmationEmail($order)
    {
        try {
            $user = Auth::user();
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            
            Mail::send('emails.order-confirmation', [
                'order' => $order,
                'user' => $user,
                'orderItems' => $orderItems
            ], function ($message) use ($user, $order) {
                $message->to($user->email, $user->name)
                        ->subject('Order Confirmation - ' . $order->order_number);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify admin about new order
     */
    private function notifyAdminNewOrder($order)
    {
        try {
            // Get admin users
            $adminUsers = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->get();

            foreach ($adminUsers as $admin) {
                Mail::send('emails.admin-new-order', [
                    'order' => $order,
                    'admin' => $admin
                ], function ($message) use ($admin, $order) {
                    $message->to($admin->email, $admin->name)
                            ->subject('New Customer Order - ' . $order->order_number);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admin about new order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 