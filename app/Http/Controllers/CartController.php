<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YogurtProduct;
use App\Models\CartItem;
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
} 