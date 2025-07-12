<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\YogurtProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->selling_price;
        });

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string|max:255',
            'delivery_contact' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
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
            // Calculate total
            $total = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->selling_price;
            });

            // Log for debugging
            Log::info('Creating order', [
                'user_id' => Auth::id(),
                'cart_items_count' => $cartItems->count(),
                'total' => $total
            ]);

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'customer',
                'order_number' => 'ORD' . now()->format('YmdHis') . strtoupper(uniqid()),
                'order_date' => now(),
                'order_status' => 'pending',
                'subtotal' => $total,
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => $total,
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_contact' => $request->delivery_contact,
                'delivery_phone' => $request->delivery_phone,
                'special_instructions' => $request->special_instructions,
                'notes' => null,
                'retailer_id' => null, // Customer orders don't have retailer
                'distribution_center_id' => 1, // Default distribution center
                'requested_delivery_date' => now()->addDays(2), // Default delivery date
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'yogurt_product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->selling_price,
                    'total_price' => $cartItem->quantity * $cartItem->product->selling_price,
                    'discount_percentage' => 0,
                    'discount_amount' => 0,
                    'final_price' => $cartItem->quantity * $cartItem->product->selling_price,
                    'production_date' => now(),
                    'expiry_date' => now()->addDays(14),
                    'item_status' => 'pending',
                    'notes' => null,
                ]);
            }

            // Clear cart
            CartItem::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully! Your cart has been cleared.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }
} 