<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YogurtProduct;

class RetailerDashboardController extends Controller
{
    public function index()
    {
        $products = YogurtProduct::where('status', 'active')->get();
        return view('dashboard-retailer', compact('products'));
    }
} 