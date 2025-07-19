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
use App\Services\BatchProductionService;
use App\Models\Delivery;
use App\Models\Driver;

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

        // Auto-select distribution center based on product availability
        $distributionCenters = \App\Models\DistributionCenter::where('status', 'operational')->get();
        $availableItems = collect();
        $unavailableItems = collect();
        foreach ($cartItems as $item) {
            $found = false;
            foreach ($distributionCenters as $center) {
                $inventory = \App\Models\Inventory::where('distribution_center_id', $center->id)
                    ->where('yogurt_product_id', $item->product->id)
                    ->first();
                if ($inventory && ($inventory->quantity_available - $inventory->quantity_reserved) >= $item->quantity) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $availableItems->push($item);
            } else {
                $unavailableItems->push($item);
            }
        }
        if ($unavailableItems->count() > 0 && !$request->has('confirm_removed')) {
            // Remove unavailable items from cart
            foreach ($unavailableItems as $item) {
                $item->delete();
            }
            // Store removed items in session for dialog
            session(['removed_items' => $unavailableItems->map(function($item) {
                return $item->product->product_name ?? 'Product';
            })->toArray()]);
            return redirect()->back()->withInput()->with('show_removed_dialog', true);
        }
        // Use only available items for the rest of the checkout
        $cartItems = $availableItems;
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'All items in your cart are out of stock.');
        }
        // Auto-select distribution center for available items
        $distributionCenterId = $this->selectDistributionCenterForCart($cartItems);
        if (!$distributionCenterId) {
            return back()->with('error', 'Unfortunately, we are unable to fulfill your order at this time because no distribution center currently has enough stock for all the items in your cart. Please adjust your cart or try again later. For assistance, contact customer support.');
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
                // Customer-friendly message
                throw new \Exception('Sorry, some items in your cart are currently out of stock. Please review your cart and try again.');
            }

            // Calculate additional costs
            $shippingCost = $this->calculateShippingCost($request->delivery_address, $distributionCenterId);
            $taxAmount = $this->calculateTax($total);
            $discountAmount = $this->calculateDiscount($total, Auth::user());
            $finalTotal = $total + $shippingCost + $taxAmount - $discountAmount;

            // Determine order type dynamically
            $totalQuantity = $cartItems->sum('quantity');
            if ($totalQuantity > 10) {
                $orderType = 'bulk';
            } elseif ($totalQuantity < 2) {
                $orderType = 'rush';
            } else {
                $orderType = 'regular';
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => $orderType,
                'order_number' => 'CUST-' . now()->format('YmdHis') . strtoupper(uniqid()),
                'order_date' => now(),
                'order_status' => 'confirmed',
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
                'distribution_center_id' => $distributionCenterId,
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
                // Removed direct deduction from $product->stock here
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

            // --- Append new order data to CSV ---
            try {
                $csvPath = storage_path('app/orders_export.csv');
                $isNewFile = !file_exists($csvPath);
                $file = fopen($csvPath, 'a');
                if ($isNewFile) {
                    // Write header
                    fputcsv($file, ['Order ID', 'Order Date', 'Product ID', 'Product Name', 'Quantity', 'Unit Price', 'Total Price']);
                }
                foreach ($cartItems as $cartItem) {
                    $product = $cartItem->product;
                    fputcsv($file, [
                        $order->id,
                        $order->created_at,
                        $cartItem->product_id,
                        $product->product_name ?? '',
                        $cartItem->quantity,
                        $product->selling_price,
                        $cartItem->quantity * $product->selling_price,
                    ]);
                }
                fclose($file);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to append order to CSV', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
            // --- End CSV append ---

            DB::commit();

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

            // --- Schedule Delivery and Assign Driver ---
            $order->refresh(); // Make sure vendor_id and distribution_center_id are up to date
            if ($order->vendor_id && $order->distribution_center_id) {
                $employeeDriver = \App\Models\Employee::where('role', 'Driver')
                    ->where('status', 'Active')
                    ->orderByRaw('(
                        SELECT COUNT(*) FROM deliveries WHERE deliveries.driver_id = employees.id AND deliveries.delivery_status IN ("scheduled", "in_transit", "out_for_delivery")
                    ) ASC')
                    ->first();
                if ($employeeDriver) {
                    Delivery::create([
                        'order_id' => $order->id,
                        'distribution_center_id' => $order->distribution_center_id,
                        'vendor_id' => $order->vendor_id,
                        'vehicle_number' => null,
                        'driver_id' => $employeeDriver->id,
                        'driver_name' => $employeeDriver->name,
                        'driver_phone' => $employeeDriver->user ? $employeeDriver->user->mobile ?? $employeeDriver->user->phone ?? null : null,
                        'driver_license' => null, // Add if available in Employee
                        'scheduled_delivery_date' => $order->requested_delivery_date ?? now()->addDay(),
                        'scheduled_delivery_time' => '09:00',
                        'delivery_address' => $order->delivery_address,
                        'recipient_name' => $order->delivery_contact,
                        'recipient_phone' => $order->delivery_phone,
                        'delivery_number' => uniqid('DEL-'),
                        'delivery_status' => 'scheduled',
                    ]);
                }
            }

            \Illuminate\Support\Facades\Log::info('Customer order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => Auth::id(),
                'total_amount' => $finalTotal
            ]);

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully! Your order number is: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order creation failed', [
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

    /**
     * Select a distribution center with sufficient stock for all products in the cart
     */
    private function selectDistributionCenterForCart($cartItems)
    {
        $distributionCenters = \App\Models\DistributionCenter::where('status', 'operational')->get();
        foreach ($distributionCenters as $center) {
            $allAvailable = true;
            foreach ($cartItems as $item) {
                $product = $item->product;
                if (!$product) {
                    $allAvailable = false;
                    break;
                }
                // Check total available inventory for this product at this center (available - reserved)
                $totalAvailable = \App\Models\Inventory::where('distribution_center_id', $center->id)
                    ->where('yogurt_product_id', $product->id)
                    ->get()
                    ->sum(function($inventory) {
                        return $inventory->quantity_available - $inventory->quantity_reserved;
                    });
                if ($totalAvailable < $item->quantity) {
                    $allAvailable = false;
                    break;
                }
            }
            if ($allAvailable) {
                return $center->id;
            }
        }
        return null; // No center found
    }
} 