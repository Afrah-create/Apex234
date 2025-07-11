<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartController extends Controller
{
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
} 