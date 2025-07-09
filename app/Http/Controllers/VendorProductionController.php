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
        $rawMaterials = \App\Models\RawMaterial::where('vendor_id', $vendor->id)->where('status', 'available')->get();
        return view('vendor.production.index', compact('batches', 'products', 'rawMaterials'));
    }

    public function create()
    {
        $vendor = Auth::user()->vendor;
        $products = YogurtProduct::all();
        $rawMaterials = RawMaterial::where('vendor_id', $vendor->id)->where('status', 'available')->get();
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
        // Add to product inventory
        $product = YogurtProduct::find($request->product_id);
        $product->stock += $request->quantity_produced;
        $product->save();
        // Create production batch
        $batch = ProductionBatch::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'quantity_produced' => $request->quantity_produced,
            'batch_code' => strtoupper(Str::random(8)),
        ]);
        // Attach raw materials
        foreach ($request->raw_materials as $rm) {
            $batch->rawMaterials()->attach($rm['id'], ['quantity_used' => $rm['quantity']]);
        }
        return redirect()->route('vendor.production.index')->with('success', 'Production batch recorded successfully!');
    }
} 