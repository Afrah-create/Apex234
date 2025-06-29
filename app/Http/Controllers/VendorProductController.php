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

    // Edit a product
    public function update(Request $request, $id): JsonResponse
    {
        $product = YogurtProduct::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'quantity_available' => 'nullable|integer|min:0',
            'quantity_reserved' => 'nullable|integer|min:0',
            'quantity_damaged' => 'nullable|integer|min:0',
            'inventory_status' => 'nullable|in:available,low_stock,out_of_stock,expired,damaged',
        ]);
        
        // Allow editing any product - remove restrictions
        if ($request->hasFile('image')) {
            if ($product->image_path) Storage::disk('public')->delete($product->image_path);
            $product->image_path = $request->file('image')->store('products', 'public');
        }
        
        $product->product_name = $request->name;
        $product->product_type = $request->type;
        $product->selling_price = $request->price;
        $product->stock = $request->stock;
        $product->save();
        
        // Update inventory quantities if provided
        $inventory = Inventory::where('yogurt_product_id', $id)->first();
        if ($inventory) {
            if ($request->filled('quantity_available')) {
                $inventory->quantity_available = $request->quantity_available;
            }
            if ($request->filled('quantity_reserved')) {
                $inventory->quantity_reserved = $request->quantity_reserved;
            }
            if ($request->filled('quantity_damaged')) {
                $inventory->quantity_damaged = $request->quantity_damaged;
            }
            
            // Auto-determine inventory status based on quantities if not explicitly set
            if ($request->filled('inventory_status')) {
                $inventory->inventory_status = $request->inventory_status;
            } else {
                // Auto-determine status based on quantities
                $available = $inventory->quantity_available;
                $damaged = $inventory->quantity_damaged;
                
                if ($damaged > 0 && $available == 0) {
                    $inventory->inventory_status = 'damaged';
                } elseif ($available == 0) {
                    $inventory->inventory_status = 'out_of_stock';
                } elseif ($available <= 10) {
                    $inventory->inventory_status = 'low_stock';
                } else {
                    $inventory->inventory_status = 'available';
                }
            }
            
            // Recalculate total value based on available quantity
            $inventory->total_value = $inventory->quantity_available * $product->selling_price;
            $inventory->last_updated = now()->toDateString();
            $inventory->save();
        }
        
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

    // Show edit form for a product
    public function show($id)
    {
        $product = YogurtProduct::findOrFail($id);
        $inventory = Inventory::where('yogurt_product_id', $id)->first();
        return view('vendor.edit-product', compact('product', 'inventory'));
    }
} 