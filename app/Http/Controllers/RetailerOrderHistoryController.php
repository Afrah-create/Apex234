<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Retailer;

class RetailerOrderHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $retailer = Retailer::where('user_id', $user->id)->first();
        $orders = [];
        if ($retailer) {
            $orders = Order::with(['orderItems.yogurtProduct'])
                ->where('retailer_id', $retailer->id)
                ->orderBy('order_date', 'desc')
                ->get();
        }
        return view('retailer.order-history', compact('orders'));
    }
} 