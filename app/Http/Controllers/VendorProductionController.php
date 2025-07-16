<?php

namespace App\Http\Controllers;

use App\Models\ProductionBatch;
use App\Models\RawMaterial;
use App\Models\YogurtProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VendorProductionController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $batches = ProductionBatch::where('vendor_id', $vendor->id)->with('product', 'rawMaterials')->latest()->get();
        $products = \App\Models\YogurtProduct::all();
        // Fetch raw materials where vendor_id matches the logged-in user's id
        $rawMaterials = \App\Models\RawMaterial::where('status', 'available')->where('vendor_id', Auth::id())->get();
        return view('vendor.production.index', compact('batches', 'products', 'rawMaterials'));
    }

    public function create()
    {
        $vendor = Auth::user()->vendor;
        $products = YogurtProduct::all();
        // Fetch raw materials where vendor_id matches the logged-in user's id
        $rawMaterials = \App\Models\RawMaterial::where('status', 'available')->where('vendor_id', Auth::id())->get();
        return view('vendor.production.create', compact('products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return back()->withErrors(['You must be an approved vendor to produce batches.'])->withInput();
        }
        $request->validate([
            'product_id' => 'required|exists:yogurt_products,id',
            'batches' => 'required|integer|min:1',
        ]);

        $product = YogurtProduct::findOrFail($request->product_id);
        $batches = $request->batches;
        $unitsPerBatch = 10;
        $totalUnits = $batches * $unitsPerBatch;

        // Get raw material requirements for this product
        $requirements = $product->rawMaterialRequirements();

        // Calculate total required for each material
        $requiredMaterials = [];
        foreach ($requirements as $type => $qtyPerBatch) {
            $requiredMaterials[$type] = $qtyPerBatch * $batches;
        }

        // Fetch vendor's available raw materials
        $materials = \App\Models\RawMaterial::where('vendor_id', $vendor->user_id)
            ->where('status', 'available')
            ->get()
            ->keyBy('material_type');

        // Check if vendor has enough of each required material
        foreach ($requiredMaterials as $type => $requiredQty) {
            if (!isset($materials[$type]) || $materials[$type]->quantity < $requiredQty) {
                return back()->withErrors(["Not enough $type in inventory. Required: $requiredQty, Available: " . ($materials[$type]->quantity ?? 0)])->withInput();
            }
        }

        DB::transaction(function () use ($vendor, $product, $batches, $totalUnits, $requiredMaterials, $materials) {
            // Deduct raw materials
            foreach ($requiredMaterials as $type => $requiredQty) {
                $material = $materials[$type];
                $material->quantity -= $requiredQty;
                $material->save();
            }

            // Create batch
            $batch = \App\Models\ProductionBatch::create([
                'vendor_id' => $vendor->id, // Use vendor's id from vendors table
                'product_id' => $product->id,
                'quantity_produced' => $totalUnits,
                'batch_code' => strtoupper(\Illuminate\Support\Str::random(8)),
            ]);

            // Attach raw materials used
            foreach ($requiredMaterials as $type => $qty) {
                $material = $materials[$type];
                $batch->rawMaterials()->attach($material->id, ['quantity_used' => $qty]);
            }

            // Add to inventory
            \App\Models\Inventory::create([
                'yogurt_product_id' => $product->id,
                'vendor_id' => $vendor->id, // Set vendor_id for inventory
                'distribution_center_id' => \App\Models\DistributionCenter::first()->id,
                'batch_number' => $batch->batch_code,
                'quantity_available' => $totalUnits,
                'quantity_reserved' => 0,
                'quantity_damaged' => 0,
                'quantity_expired' => 0,
                'production_date' => now()->toDateString(),
                'expiry_date' => now()->addDays(7)->toDateString(),
                'storage_temperature' => 4.0,
                'storage_location' => 'refrigerator',
                'shelf_location' => null,
                'inventory_status' => 'available',
                'unit_cost' => $product->production_cost ?? 0,
                'total_value' => ($product->production_cost ?? 0) * $totalUnits,
                'last_updated' => now()->toDateString(),
                'notes' => 'Batch created from production',
            ]);
        });

        return redirect()->route('vendor.production.index')->with('success', 'Production batch recorded successfully!');
    }

    public function reserveBatch(Request $request, $id)
    {
        $request->validate([
            'reserve_quantity' => 'required|integer|min:1',
        ]);

        $inventory = \App\Models\Inventory::findOrFail($id);

        if ($request->reserve_quantity > $inventory->quantity_available) {
            return back()->withErrors(['reserve_quantity' => 'Not enough available quantity to reserve.']);
        }

        $inventory->quantity_available -= $request->reserve_quantity;
        $inventory->quantity_reserved += $request->reserve_quantity;
        $inventory->save();

        return back()->with('success', 'Batch reserved successfully!');
    }
} 