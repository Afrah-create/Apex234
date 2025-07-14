<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\YogurtProduct;
use App\Models\Inventory;
use App\Models\RawMaterial;
use App\Models\ProductionFacility;
use App\Models\DistributionCenter;

class VendorInventoryController extends Controller
{
    // Only allow the three products
    private $allowedProducts = [
        ['product_name' => 'Greek Vanilla Yoghurt', 'product_code' => 'P0001'],
        ['product_name' => 'Low Fat Blueberry Yoghurt', 'product_code' => 'P0002'],
        ['product_name' => 'Organic Strawberry Yoghurt', 'product_code' => 'P0003'],
    ];

    // Get all inventory data (products and raw materials)
    public function index(): JsonResponse
    {
        $vendorId = Auth::id();
        
        // Get product inventory
        $productInventory = Inventory::with(['yogurtProduct'])
            ->whereHas('yogurtProduct', function($query) {
                $query->whereIn('product_name', array_column($this->allowedProducts, 'product_name'));
            })
            ->get()
            ->map(function($inventory) {
                return [
                    'id' => $inventory->id,
                    'yogurt_product_id' => $inventory->yogurt_product_id,
                    'type' => 'product',
                    'product_name' => $inventory->yogurtProduct->product_name,
                    'product_type' => $inventory->yogurtProduct->product_type,
                    'batch_number' => $inventory->batch_number,
                    'quantity_available' => $inventory->quantity_available,
                    'quantity_reserved' => $inventory->quantity_reserved,
                    'quantity_damaged' => $inventory->quantity_damaged,
                    'quantity_expired' => $inventory->quantity_expired,
                    'production_date' => $inventory->production_date,
                    'expiry_date' => $inventory->expiry_date,
                    'storage_temperature' => $inventory->storage_temperature,
                    'storage_location' => $inventory->storage_location,
                    'inventory_status' => $inventory->inventory_status,
                    'unit_cost' => $inventory->unit_cost,
                    'total_value' => $inventory->total_value,
                    'last_updated' => $inventory->last_updated,
                    'notes' => $inventory->notes,
                ];
            });

        // Get raw materials inventory
        $rawMaterials = DB::table('raw_materials')
            ->select([
                'id',
                'material_name',
                'material_type',
                'quantity',
                'unit_of_measure',
                'unit_price',
                'total_cost',
                'harvest_date',
                'expiry_date',
                'quality_grade',
                'temperature',
                'ph_level',
                'fat_content',
                'protein_content',
                'status',
                'quality_notes',
                'created_at',
                'updated_at'
            ])
            ->where('vendor_id', $vendorId)
            ->where('status', 'available')
            ->get()
            ->map(function($material) {
                return [
                    'id' => $material->id,
                    'type' => 'raw_material',
                    'material_name' => $material->material_name,
                    'material_type' => $material->material_type,
                    'quantity' => $material->quantity,
                    'unit_of_measure' => $material->unit_of_measure,
                    'unit_price' => $material->unit_price,
                    'total_cost' => $material->total_cost,
                    'harvest_date' => $material->harvest_date,
                    'expiry_date' => $material->expiry_date,
                    'quality_grade' => $material->quality_grade,
                    'temperature' => $material->temperature,
                    'ph_level' => $material->ph_level,
                    'fat_content' => $material->fat_content,
                    'protein_content' => $material->protein_content,
                    'status' => $material->status,
                    'quality_notes' => $material->quality_notes,
                    'last_updated' => $material->updated_at,
                ];
            });

        return response()->json([
            'product_inventory' => $productInventory,
            'raw_materials' => $rawMaterials
        ]);
    }

    // Create new product inventory
    public function storeProductInventory(Request $request): JsonResponse
    {
        $request->validate([
            'product_name' => 'required|string|in:' . implode(',', array_column($this->allowedProducts, 'product_name')),
            'batch_number' => 'required|string|unique:inventories,batch_number',
            'quantity_available' => 'required|integer|min:0',
            'quantity_reserved' => 'integer|min:0',
            'quantity_damaged' => 'integer|min:0',
            'quantity_expired' => 'integer|min:0',
            'production_date' => 'required|date',
            'expiry_date' => 'required|date|after:production_date',
            'storage_temperature' => 'required|numeric|between:-10,20',
            'storage_location' => 'required|in:cold_room,refrigerator,freezer,warehouse',
            'shelf_location' => 'nullable|string',
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Get the product
        $product = YogurtProduct::where('product_name', $request->product_name)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Get or create distribution center
        $distributionCenter = DistributionCenter::firstOrCreate(
            ['center_code' => 'DC001'],
            [
                'center_name' => 'Main Distribution Center',
                'center_address' => 'Kampala, Uganda',
                'center_phone' => '+256-XXX-XXX-XXX',
                'center_email' => 'dc@caramel-yg.com',
                'center_manager' => 'Distribution Manager',
                'manager_phone' => '+256-XXX-XXX-XXX',
                'manager_email' => 'manager@caramel-yg.com',
                'center_type' => 'primary',
                'storage_capacity' => 1000,
                'current_inventory' => 0,
                'temperature_control' => 4.0,
                'delivery_vehicles' => 5,
                'delivery_radius' => 50,
                'status' => 'operational'
            ]
        );

        // Calculate total value
        $totalValue = $request->quantity_available * $request->unit_cost;

        // Determine inventory status
        $inventoryStatus = 'available';
        if ($request->quantity_available <= 10) {
            $inventoryStatus = 'low_stock';
        } elseif ($request->quantity_available == 0) {
            $inventoryStatus = 'out_of_stock';
        }

        // Create inventory record
        $inventory = Inventory::create([
            'yogurt_product_id' => $product->id,
            'distribution_center_id' => $distributionCenter->id,
            'batch_number' => $request->batch_number,
            'quantity_available' => $request->quantity_available,
            'quantity_reserved' => $request->quantity_reserved ?? 0,
            'quantity_damaged' => $request->quantity_damaged ?? 0,
            'quantity_expired' => $request->quantity_expired ?? 0,
            'production_date' => $request->production_date,
            'expiry_date' => $request->expiry_date,
            'storage_temperature' => $request->storage_temperature,
            'storage_location' => $request->storage_location,
            'shelf_location' => $request->shelf_location,
            'inventory_status' => $inventoryStatus,
            'unit_cost' => $request->unit_cost,
            'total_value' => $totalValue,
            'last_updated' => now()->toDateString(),
            'notes' => $request->notes,
        ]);

        // Update product stock
        $product->stock = $product->stock + $request->quantity_available;
        $product->save();

        return response()->json([
            'success' => true,
            'inventory' => $inventory->load('yogurtProduct')
        ]);
    }

    // Update product inventory
    public function updateProductInventory(Request $request, $id): JsonResponse
    {
        $inventory = Inventory::findOrFail($id);
        
        $request->validate([
            'quantity_available' => 'required|integer|min:0',
            'quantity_reserved' => 'integer|min:0',
            'quantity_damaged' => 'integer|min:0',
            'quantity_expired' => 'integer|min:0',
            'storage_temperature' => 'numeric|between:-10,20',
            'storage_location' => 'in:cold_room,refrigerator,freezer,warehouse',
            'shelf_location' => 'nullable|string',
            'unit_cost' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate stock difference
        $oldQuantity = $inventory->quantity_available;
        $newQuantity = $request->quantity_available;
        $quantityDifference = $newQuantity - $oldQuantity;

        // Update inventory
        $inventory->quantity_available = $newQuantity;
        $inventory->quantity_reserved = $request->quantity_reserved ?? $inventory->quantity_reserved;
        $inventory->quantity_damaged = $request->quantity_damaged ?? $inventory->quantity_damaged;
        $inventory->quantity_expired = $request->quantity_expired ?? $inventory->quantity_expired;
        $inventory->storage_temperature = $request->storage_temperature ?? $inventory->storage_temperature;
        $inventory->storage_location = $request->storage_location ?? $inventory->storage_location;
        $inventory->shelf_location = $request->shelf_location ?? $inventory->shelf_location;
        $inventory->unit_cost = $request->unit_cost ?? $inventory->unit_cost;
        $inventory->total_value = $newQuantity * $inventory->unit_cost;
        $inventory->last_updated = now()->toDateString();
        $inventory->notes = $request->notes ?? $inventory->notes;

        // Determine inventory status
        if ($newQuantity <= 10) {
            $inventory->inventory_status = 'low_stock';
        } elseif ($newQuantity == 0) {
            $inventory->inventory_status = 'out_of_stock';
        } else {
            $inventory->inventory_status = 'available';
        }

        $inventory->save();

        // Update product stock
        $product = $inventory->yogurtProduct;
        $product->stock = $product->stock + $quantityDifference;
        $product->save();

        return response()->json([
            'success' => true,
            'inventory' => $inventory->load('yogurtProduct')
        ]);
    }

    // Delete product inventory
    public function deleteProductInventory($id): JsonResponse
    {
        $inventory = Inventory::findOrFail($id);
        
        // Update product stock
        $product = $inventory->yogurtProduct;
        $product->stock = $product->stock - $inventory->quantity_available;
        $product->save();

        $inventory->delete();

        return response()->json(['success' => true]);
    }

    // Create new raw material
    public function storeRawMaterial(Request $request): JsonResponse
    {
        // Vendors are not allowed to add raw materials directly
        return response()->json([
            'success' => false,
            'error' => 'You are not allowed to add raw materials directly. Please order from a supplier.'
        ], 403);
    }

    // Update raw material
    public function updateRawMaterial(Request $request, $id): JsonResponse
    {
        $request->validate([
            'material_name' => 'nullable|string|max:255',
            'material_type' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit_of_measure' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'harvest_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'quality_grade' => 'nullable|string|max:10',
            'temperature' => 'nullable|numeric',
            'ph_level' => 'nullable|numeric',
            'fat_content' => 'nullable|numeric|min:0',
            'protein_content' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:available,in_use,expired,disposed',
            'quality_notes' => 'nullable|string',
        ]);

        // Calculate total cost
        $totalCost = $request->quantity * $request->unit_price;

        $updateData = [
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_cost' => $totalCost,
            'updated_at' => now(),
        ];

        // Only update fields that are provided and not empty
        if ($request->filled('material_name')) {
            $updateData['material_name'] = $request->material_name;
        }
        if ($request->filled('material_type')) {
            $updateData['material_type'] = $request->material_type;
        }
        if ($request->filled('unit_of_measure')) {
            $updateData['unit_of_measure'] = $request->unit_of_measure;
        }
        if ($request->filled('harvest_date')) {
            $updateData['harvest_date'] = $request->harvest_date;
        }
        if ($request->filled('expiry_date')) {
            $updateData['expiry_date'] = $request->expiry_date;
        }
        if ($request->filled('quality_grade')) {
            $updateData['quality_grade'] = $request->quality_grade;
        }
        if ($request->filled('temperature')) {
            $updateData['temperature'] = $request->temperature;
        }
        if ($request->filled('ph_level')) {
            $updateData['ph_level'] = $request->ph_level;
        }
        if ($request->filled('fat_content')) {
            $updateData['fat_content'] = $request->fat_content;
        }
        if ($request->filled('protein_content')) {
            $updateData['protein_content'] = $request->protein_content;
        }
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }
        if ($request->filled('quality_notes')) {
            $updateData['quality_notes'] = $request->quality_notes;
        }
        if ($request->filled('available')) {
            $updateData['available'] = $request->available;
        }
        if ($request->filled('in_use')) {
            $updateData['in_use'] = $request->in_use;
        }
        if ($request->filled('expired')) {
            $updateData['expired'] = $request->expired;
        }
        if ($request->filled('disposed')) {
            $updateData['disposed'] = $request->disposed;
        }

        DB::table('raw_materials')
            ->where('id', $id)
            ->update($updateData);

        $rawMaterial = DB::table('raw_materials')->find($id);

        return response()->json([
            'success' => true,
            'raw_material' => $rawMaterial
        ]);
    }

    // Delete raw material
    public function deleteRawMaterial($id): JsonResponse
    {
        $vendorId = Auth::id();
        $deleted = DB::table('raw_materials')
            ->where('id', $id)
            ->where('vendor_id', $vendorId)
            ->delete();
        return response()->json(['success' => $deleted > 0]);
    }

    // Get inventory summary for dashboards
    public function getInventorySummary(): JsonResponse
    {
        $vendorId = Auth::id();
        
        // Product inventory summary
        $productSummary = Inventory::with(['yogurtProduct'])
            ->whereHas('yogurtProduct', function($query) {
                $query->whereIn('product_name', array_column($this->allowedProducts, 'product_name'));
            })
            ->selectRaw('
                SUM(quantity_available) as total_available,
                SUM(quantity_reserved) as total_reserved,
                SUM(quantity_damaged) as total_damaged,
                SUM(quantity_expired) as total_expired,
                SUM(total_value) as total_value,
                COUNT(*) as total_batches
            ')
            ->first();

        // Raw materials summary
        $rawMaterialSummary = DB::table('raw_materials')
            ->selectRaw('
                SUM(CASE WHEN status = "available" THEN quantity ELSE 0 END) as available_quantity,
                SUM(CASE WHEN status = "in_use" THEN quantity ELSE 0 END) as in_use_quantity,
                SUM(CASE WHEN status = "expired" THEN quantity ELSE 0 END) as expired_quantity,
                SUM(CASE WHEN status = "disposed" THEN quantity ELSE 0 END) as disposed_quantity,
                SUM(total_cost) as total_cost,
                COUNT(*) as total_materials
            ')
            ->first();

        return response()->json([
            'product_summary' => $productSummary,
            'raw_material_summary' => $rawMaterialSummary
        ]);
    }

    // Get inventory chart data
    public function getInventoryChartData(): JsonResponse
    {
        // Product inventory by status
        $productStatusData = Inventory::with(['yogurtProduct'])
            ->whereHas('yogurtProduct', function($query) {
                $query->whereIn('product_name', array_column($this->allowedProducts, 'product_name'));
            })
            ->selectRaw('inventory_status, SUM(quantity_available) as total_quantity')
            ->groupBy('inventory_status')
            ->get();

        // Raw materials by type
        $rawMaterialTypeData = DB::table('raw_materials')
            ->selectRaw('material_type, SUM(quantity) as total_quantity')
            ->groupBy('material_type')
            ->get();

        // Raw materials by status
        $rawMaterialStatusData = DB::table('raw_materials')
            ->selectRaw('status, SUM(quantity) as total_quantity')
            ->groupBy('status')
            ->get();

        return response()->json([
            'product_status_data' => $productStatusData,
            'raw_material_type_data' => $rawMaterialTypeData,
            'raw_material_status_data' => $rawMaterialStatusData
        ]);
    }

    // Get all dairy farms for the form
    public function getDairyFarms(): JsonResponse
    {
        $farms = \App\Models\DairyFarm::select('id', 'farm_name')->get();
        return response()->json($farms);
    }
} 