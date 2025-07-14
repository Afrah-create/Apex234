<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YogurtProduct;

class CustomerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = YogurtProduct::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%") ;
            });
        }
        $products = $query->get();
        return view('customer.dashboard', compact('products'))->with('search', $request->input('search'));
    }
} 