<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YogurtProduct;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $products = YogurtProduct::all();
        return view('customer.dashboard', compact('products'));
    }
} 