<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YogurtProduct;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    // Show all products
    public function index()
    {
        $products = YogurtProduct::all();
        return view('admin.products.index', compact('products'));
    }

    // Update product details (name, price, discount)
    public function update(Request $request, YogurtProduct $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:2048',
        ]);
        $product->product_name = $validated['product_name'];
        $product->selling_price = $validated['selling_price'];
        $product->discount = $validated['discount'] ?? null;
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $request->file('image')->store('products', 'public');
        }
        $product->save();
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }
} 