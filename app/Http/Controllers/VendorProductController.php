<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\YogurtProduct;
use App\Models\Inventory;

class VendorProductController extends Controller
{
    // Only allow the three products
    private $allowedProducts = [
        ['product_name' => 'Greek Vanilla Yoghurt', 'product_code' => 'P0001'],
        ['product_name' => 'Low Fat Blueberry Yoghurt', 'product_code' => 'P0002'],
        ['product_name' => 'Organic Strawberry Yoghurt', 'product_code' => 'P0003'],
    ];

    // List vendor's products
    public function index(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json([]);
        }
        // Get product IDs from inventories for this vendor
        $productIds = \App\Models\Inventory::where('vendor_id', $vendor->id)
            ->pluck('yogurt_product_id')
            ->unique();
        $products = YogurtProduct::whereIn('id', $productIds)
            ->where('status', '!=', 'deleted')
            ->get();
        return response()->json($products);
    }

    // Add a new product
    public function store(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        // Check if product_name and product_code are in allowedProducts
        if (!in_array($request->name, array_column($this->allowedProducts, 'product_name'))) {
            abort(403, 'Only the three main products are allowed.');
        }
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
            'vendor_id' => $vendor->id,
        ]);
        // Create inventory record for the new product
        Inventory::create([
            'yogurt_product_id' => $product->id,
            'distribution_center_id' => 1, // Placeholder, adjust as needed
            'batch_number' => uniqid('BATCH-'),
            'quantity_available' => $request->stock,
            'quantity_reserved' => 0,
            'quantity_damaged' => 0,
            'quantity_expired' => 0,
            'production_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(30)->toDateString(),
            'storage_temperature' => 4.0,
            'storage_location' => 'refrigerator',
            'shelf_location' => null,
            'inventory_status' => 'available',
            'unit_cost' => $request->price,
            'total_value' => $request->price * $request->stock,
            'last_updated' => now()->toDateString(),
            'notes' => null,
        ]);
        return response()->json(['success' => true, 'product' => $product]);
    }

    // Update a product for the vendor
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }
        // Ensure the product is associated with the vendor via inventory
        $hasInventory = \App\Models\Inventory::where('vendor_id', $vendor->id)
            ->where('yogurt_product_id', $id)
            ->exists();
        if (!$hasInventory) {
            return response()->json(['error' => 'Product not found for this vendor'], 404);
        }
        $product = YogurtProduct::findOrFail($id);
        // Map request fields to correct DB columns
        $product->product_name = $request->input('product_name', $product->product_name);
        $product->product_type = $request->input('product_type', $product->product_type);
        $product->flavor = $request->input('flavor', $product->flavor);
        $product->fat_content = $request->input('fat_content', $product->fat_content);
        $product->protein_content = $request->input('protein_content', $product->protein_content);
        $product->sugar_content = $request->input('sugar_content', $product->sugar_content);
        $product->calories_per_100g = $request->input('calories_per_100g', $product->calories_per_100g);
        $product->package_size = $request->input('package_size', $product->package_size);
        $product->package_type = $request->input('package_type', $product->package_type);
        $product->shelf_life_days = $request->input('shelf_life_days', $product->shelf_life_days);
        $product->storage_temperature = $request->input('storage_temperature', $product->storage_temperature);
        $product->ingredients = $request->input('ingredients', $product->ingredients);
        $product->nutritional_info = $request->input('nutritional_info', $product->nutritional_info);
        $product->allergens = $request->input('allergens', $product->allergens);
        $product->production_cost = $request->input('production_cost', $product->production_cost);
        $product->product_code = $request->input('product_code', $product->product_code);
        $product->selling_price = $request->input('selling_price', $product->selling_price);
        $product->discount = $request->input('discount', $product->discount);
        $product->status = $request->input('status', $product->status);
        $product->notes = $request->input('notes', $product->notes);
        $product->production_facility_id = $request->input('production_facility_id', $product->production_facility_id);
        $product->image_path = $request->input('image_path', $product->image_path);
        $product->save();
        return response()->json($product);
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

    // Show edit form for a product
    public function show($id): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }
        // Ensure the product is associated with the vendor via inventory
        $hasInventory = \App\Models\Inventory::where('vendor_id', $vendor->id)
            ->where('yogurt_product_id', $id)
            ->exists();
        if (!$hasInventory) {
            return response()->json(['error' => 'Product not found for this vendor'], 404);
        }
        $product = YogurtProduct::findOrFail($id);
        return response()->json($product);
    }

    // Show the edit form for a product for the vendor
    public function edit($id)
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            abort(404, 'Vendor not found');
        }
        // Ensure the product is associated with the vendor via inventory
        $hasInventory = \App\Models\Inventory::where('vendor_id', $vendor->id)
            ->where('yogurt_product_id', $id)
            ->exists();
        if (!$hasInventory) {
            abort(404, 'Product not found for this vendor');
        }
        $product = YogurtProduct::findOrFail($id);
        return view('vendor.edit-product', compact('product'));
    }
} 