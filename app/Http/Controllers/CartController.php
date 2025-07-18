<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YogurtProduct;
use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Show cart contents
    public function index()
    {
        if (Auth::check()) {
            $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get()->map(function ($item) {
                return [
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->product->selling_price,
                ];
            });
            $total = $cartItems->sum('subtotal');
        } else {
            $cart = session()->get('cart', []);
            $products = YogurtProduct::whereIn('id', array_keys($cart))->get();
            $cartItems = $products->map(function ($product) use ($cart) {
                $quantity = $cart[$product->id]['quantity'] ?? 1;
                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $quantity * $product->selling_price,
                ];
            });
            $total = $cartItems->sum('subtotal');
        }
        return view('cart.index', compact('cartItems', 'total'));
    }

    // Add product to cart
    public function add(Request $request, YogurtProduct $product)
    {
        $quantity = (int) $request->input('quantity', 1);
        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())->where('product_id', $product->id)->first();
            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $quantity;
            } else {
                $cart[$product->id] = [
                    'quantity' => $quantity,
                ];
            }
            session(['cart' => $cart]);
        }
        return redirect()->back()->with('success', 'Product added to cart!');
    }

    // Update product quantity in cart
    public function update(Request $request, YogurtProduct $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())->where('product_id', $product->id)->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] = $quantity;
                session(['cart' => $cart]);
            }
        }
        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }

    // Remove product from cart
    public function remove(YogurtProduct $product)
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->where('product_id', $product->id)->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$product->id])) {
                unset($cart[$product->id]);
                session(['cart' => $cart]);
            }
        }
        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }

    // Add this method to handle proceed to checkout
    public function proceedToCheckout()
    {
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();
        $removed = [];
        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                $removed[] = $item->product ? $item->product->product_name : 'Unknown Product';
                $item->delete();
            }
        }
        $remainingItems = CartItem::where('user_id', Auth::id())->count();
        if ($remainingItems === 0) {
            return redirect()->route('cart.index')->with('error', 'All items in your cart are out of stock and have been removed. Please add available products to your cart.');
        }
        if (!empty($removed)) {
            session(['removed_cart_items' => $removed]);
            return redirect()->route('cart.removedItems');
        }
        return redirect()->route('checkout.index');
    }

    // API methods for cart management
    public function getCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        return response()->json(['cart' => $cart ? $cart->cart_data : []]);
    }

    public function saveCart(Request $request)
    {
        $request->validate(['cart' => 'required|array']);
        $cart = Cart::updateOrCreate(
            ['user_id' => Auth::id()],
            ['cart_data' => $request->cart]
        );
        return response()->json(['success' => true]);
    }

    public function removedItems()
    {
        $removed = session('removed_cart_items', []);
        if (empty($removed)) {
            return redirect()->route('cart.index');
        }
        return view('cart.removed-items', compact('removed'));
    }
} 