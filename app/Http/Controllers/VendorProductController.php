<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\YogurtProduct;

class VendorProductController extends Controller
{
    // List vendor's products
    public function index(): JsonResponse
    {
        $products = YogurtProduct::where('status', '!=', 'deleted')->get();
        return response()->json($products);
    }

    // Add a new product
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $product = YogurtProduct::create([
            'product_name' => $request->name,
            'product_type' => $request->type,
            'selling_price' => $request->price,
            'status' => 'active',
            'stock' => $request->stock,
            'image_path' => $imagePath,
            'production_facility_id' => 1, // Placeholder, adjust as needed
            'product_code' => uniqid('YOG-'),
        ]);
        return response()->json(['success' => true, 'product' => $product]);
    }

    // Edit a product
    public function update(Request $request, $id): JsonResponse
    {
        $product = YogurtProduct::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($product->image_path) Storage::disk('public')->delete($product->image_path);
            $product->image_path = $request->file('image')->store('products', 'public');
        }
        $product->product_name = $request->name;
        $product->product_type = $request->type;
        $product->selling_price = $request->price;
        $product->stock = $request->stock;
        $product->save();
        return response()->json(['success' => true, 'product' => $product]);
    }

    // Delete a product
    public function destroy($id): JsonResponse
    {
        $product = YogurtProduct::findOrFail($id);
        $product->status = 'deleted';
        $product->save();
        return response()->json(['success' => true]);
    }

    // Toggle product status
    public function toggleStatus($id): JsonResponse
    {
        $product = YogurtProduct::findOrFail($id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();
        return response()->json(['success' => true, 'status' => $product->status]);
    }
} 