<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\YogurtProduct;
use App\Models\DistributionCenter;
use App\Services\OrderProcessingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetailerCheckoutController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('retailer.cart.index')->with('error', 'Your cart is empty.');
        }
        $inventoryIssues = [];
        foreach ($cartItems as $item) {
            $product = $item->product;
            if (!$product) {
                $inventoryIssues[] = "Product not found.";
                continue;
            }
            $availableStock = $product->stock ?? 0;
            if ($availableStock < $item->quantity) {
                $inventoryIssues[] = "Insufficient stock for {$product->product_name}. Available: {$availableStock}, Requested: {$item->quantity}";
            }
        }
        if (!empty($inventoryIssues)) {
            return redirect()->route('retailer.cart.index')->with('error', 'Inventory issues: ' . implode(', ', $inventoryIssues));
        }
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->selling_price;
        });
        $distributionCenters = DistributionCenter::where('status', 'operational')->get();
        return view('retailer.checkout', compact('cartItems', 'total', 'distributionCenters'));
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
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('retailer.cart.index')->with('error', 'Your cart is empty.');
        }
        $distributionCenters = DistributionCenter::where('status', 'operational')->get();
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
            foreach ($unavailableItems as $item) {
                $item->delete();
            }
            session(['removed_items' => $unavailableItems->map(function($item) {
                return $item->product->product_name ?? 'Product';
            })->toArray()]);
            return redirect()->back()->withInput()->with('show_removed_dialog', true);
        }
        $cartItems = $availableItems;
        if ($cartItems->isEmpty()) {
            return redirect()->route('retailer.cart.index')->with('error', 'All items in your cart are out of stock.');
        }
        $distributionCenterId = $this->selectDistributionCenterForCart($cartItems);
        if (!$distributionCenterId) {
            return back()->with('error', 'Unfortunately, we are unable to fulfill your order at this time because no distribution center currently has enough stock for all the items in your cart. Please adjust your cart or try again later. For assistance, contact customer support.');
        }
        DB::beginTransaction();
        try {
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
                throw new \Exception('Sorry, some items in your cart are currently out of stock. Please review your cart and try again.');
            }
            $shippingCost = 5000; // Flat for now
            $taxAmount = round($total * 0.18);
            $discountAmount = 0;
            $finalTotal = $total + $shippingCost + $taxAmount - $discountAmount;
            $totalQuantity = $cartItems->sum('quantity');
            $orderType = 'regular';
            $order = Order::create([
                'retailer_id' => Auth::user()->retailer->id ?? null,
                'order_type' => $orderType,
                'order_number' => 'RET-' . now()->format('YmdHis') . strtoupper(uniqid()),
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
                'notes' => 'Retailer order placed via checkout',
                'distribution_center_id' => $distributionCenterId,
                'requested_delivery_date' => $request->requested_delivery_date,
            ]);
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
            }
            CartItem::where('user_id', Auth::id())->delete();
            $orderProcessingService = new OrderProcessingService();
            $orderProcessingService->processRetailerOrder($order);
            DB::commit();
            return redirect()->route('retailer.orders.history')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    private function selectDistributionCenterForCart($cartItems)
    {
        $distributionCenters = DistributionCenter::where('status', 'operational')->get();
        foreach ($distributionCenters as $center) {
            $allAvailable = true;
            foreach ($cartItems as $item) {
                $product = $item->product;
                if (!$product) {
                    $allAvailable = false;
                    break;
                }
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
        return null;
    }
} 