<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\DairyFarm;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function submitMilkBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|string|unique:raw_materials,material_code',
            'quantity' => 'required|numeric|min:0.01',
        ]);
        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }
        $dairyFarm = DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return response()->json(['success' => false, 'message' => 'Dairy farm not found.'], 404);
        }
        $rawMaterial = RawMaterial::create([
            'dairy_farm_id' => $dairyFarm->id,
            'material_name' => 'Milk',
            'material_code' => $request->batch_id,
            'material_type' => 'milk',
            'description' => 'Milk batch submitted by supplier',
            'quantity' => $request->quantity,
            'unit_of_measure' => 'liters',
            'unit_price' => 0,
            'total_cost' => 0,
            'harvest_date' => now()->toDateString(),
            'expiry_date' => now()->toDateString(),
            'quality_grade' => 'A',
            'temperature' => null,
            'ph_level' => null,
            'fat_content' => null,
            'protein_content' => null,
            'status' => 'Pending',
            'quality_notes' => null,
        ]);
        return response()->json(['success' => true, 'raw_material' => $rawMaterial]);
    }

    public function milkBatchHistory(Request $request)
    {
        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return response()->json(['history' => []]);
        }
        $dairyFarm = DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return response()->json(['history' => []]);
        }
        $batches = RawMaterial::where('dairy_farm_id', $dairyFarm->id)
            ->where('material_type', 'milk')
            ->orderByDesc('created_at')
            ->get(['id', 'material_code as batch_id', 'quantity', 'status'])
            ->map(function($row) {
                return [
                    'id' => $row->id,
                    'batch_id' => $row->batch_id,
                    'quantity' => $row->quantity,
                    'delivery_status' => $row->status,
                ];
            });
        return response()->json(['history' => $batches]);
    }

    public function updateMilkBatchStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Delivered',
        ]);
        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }
        $dairyFarm = DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return response()->json(['success' => false, 'message' => 'Dairy farm not found.'], 404);
        }
        $batch = RawMaterial::where('id', $id)
            ->where('dairy_farm_id', $dairyFarm->id)
            ->where('material_type', 'milk')
            ->first();
        if (!$batch) {
            return response()->json(['success' => false, 'message' => 'Batch not found.'], 404);
        }
        $batch->status = $request->status;
        $batch->save();
        return response()->json(['success' => true, 'batch' => $batch]);
    }
}
