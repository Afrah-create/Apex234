<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\YogurtProduct;
use Illuminate\Support\Facades\Auth;

class RetailerCartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get()->map(function ($item) {
            return [
                'product' => $item->product,
                'quantity' => $item->quantity,
                'subtotal' => $item->quantity * $item->product->selling_price,
            ];
        });
        $total = $cartItems->sum('subtotal');
        return view('retailer.cart', compact('cartItems', 'total'));
    }

    public function remove(YogurtProduct $product)
    {
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->where('product_id', $product->id)->delete();
        }
        return redirect()->route('retailer.cart.index')->with('success', 'Product removed from cart!');
    }
} 