<?php

namespace App\Http\Controllers;

use App\Models\ProductionBatch;
use App\Models\RawMaterial;
use App\Models\YogurtProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VendorProductionController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $batches = ProductionBatch::where('vendor_id', $vendor->id)->with('product', 'rawMaterials')->latest()->get();
        $products = \App\Models\YogurtProduct::all();
        // Allow using raw materials from any vendor
        $rawMaterials = \App\Models\RawMaterial::where('status', 'available')->get();
        return view('vendor.production.index', compact('batches', 'products', 'rawMaterials'));
    }

    public function create()
    {
        $vendor = Auth::user()->vendor;
        $products = YogurtProduct::all();
        // Allow using raw materials from any vendor
        $rawMaterials = \App\Models\RawMaterial::where('status', 'available')->get();
        return view('vendor.production.create', compact('products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $request->validate([
            'product_id' => 'required|exists:yogurt_products,id',
            'quantity_produced' => 'required|integer|min:1',
            'raw_materials' => 'required|array',
            'raw_materials.*.id' => 'required|exists:raw_materials,id',
            'raw_materials.*.quantity' => 'required|integer|min:1',
        ]);
        // Check raw material availability
        foreach ($request->raw_materials as $rm) {
            $rawMaterial = RawMaterial::find($rm['id']);
            if ($rawMaterial->quantity < $rm['quantity']) {
                return back()->withErrors(["Not enough {$rawMaterial->material_type} in inventory."])->withInput();
            }
        }
        // Deduct raw materials
        foreach ($request->raw_materials as $rm) {
            $rawMaterial = RawMaterial::find($rm['id']);
            $rawMaterial->quantity -= $rm['quantity'];
            $rawMaterial->save();
        }
        // Add to product inventory (create inventory record)
        $product = YogurtProduct::find($request->product_id);
        // Find a distribution center (for now, use the first one)
        $distributionCenterId = \App\Models\DistributionCenter::first()->id ?? 1;
        $batchNumber = strtoupper(Str::random(8));
        $productionDate = now()->toDateString();
        $expiryDate = now()->addDays(30)->toDateString(); // Default 30 days, adjust as needed
        $unitCost = $product->production_cost ?? 0;
        $totalValue = $unitCost * $request->quantity_produced;
        
        \App\Models\Inventory::create([
            'yogurt_product_id' => $product->id,
            'distribution_center_id' => $distributionCenterId,
            'batch_number' => $batchNumber,
            'quantity_available' => $request->quantity_produced,
            'quantity_reserved' => 0,
            'quantity_damaged' => 0,
            'quantity_expired' => 0,
            'production_date' => $productionDate,
            'expiry_date' => $expiryDate,
            'storage_temperature' => 4.0,
            'storage_location' => 'refrigerator',
            'shelf_location' => null,
            'inventory_status' => 'available',
            'unit_cost' => $unitCost,
            'total_value' => $totalValue,
            'last_updated' => $productionDate,
            'notes' => 'Batch created from production',
        ]);
        // Create production batch
        $batch = ProductionBatch::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'quantity_produced' => $request->quantity_produced,
            'batch_code' => $batchNumber,
        ]);
        // Attach raw materials
        foreach ($request->raw_materials as $rm) {
            $batch->rawMaterials()->attach($rm['id'], ['quantity_used' => $rm['quantity']]);
        }
        return redirect()->route('vendor.production.index')->with('success', 'Production batch recorded successfully!');
    }
} 