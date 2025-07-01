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
            'expiry_date' => now()->addDays(7)->toDateString(),
            'quality_grade' => 'A',
            'temperature' => null,
            'ph_level' => null,
            'fat_content' => null,
            'protein_content' => null,
            'status' => 'available',
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
            'status' => 'required|in:available,in_use,expired,disposed',
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

    /**
     * Get all raw material inventory for the authenticated supplier.
     */
    public function rawMaterialInventory(Request $request)
    {
        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return response()->json(['inventory' => []]);
        }
        $dairyFarm = \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return response()->json(['inventory' => []]);
        }
        $materials = \App\Models\RawMaterial::where('dairy_farm_id', $dairyFarm->id)
            ->whereIn('material_type', ['milk', 'sugar', 'fruit'])
            ->orderByDesc('created_at')
            ->get(['id', 'material_name', 'material_type', 'material_code', 'quantity', 'unit_of_measure', 'harvest_date', 'expiry_date', 'quality_grade', 'status']);
        return response()->json(['inventory' => $materials]);
    }

    /**
     * Store a new raw material for the authenticated supplier.
     */
    public function storeRawMaterial(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string|max:255',
            'material_type' => 'required|in:milk,sugar,fruit',
            'material_code' => 'required|string|max:255|unique:raw_materials,material_code',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'harvest_date' => 'required|date',
            'expiry_date' => 'required|date',
            'quality_grade' => 'required|in:A,B,C,D',
            'temperature' => 'nullable|numeric',
            'ph_level' => 'nullable|numeric|min:0|max:14',
            'fat_content' => 'nullable|numeric|min:0|max:100',
            'protein_content' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:available,in_use,expired,disposed',
            'quality_notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }
        $dairyFarm = \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return response()->json(['success' => false, 'message' => 'Dairy farm not found.'], 404);
        }

        // Calculate total cost
        $totalCost = $request->quantity * $request->unit_price;

        $rawMaterial = \App\Models\RawMaterial::create([
            'dairy_farm_id' => $dairyFarm->id,
            'material_name' => $request->material_name,
            'material_type' => $request->material_type,
            'material_code' => $request->material_code,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_of_measure' => $request->unit_of_measure,
            'unit_price' => $request->unit_price,
            'total_cost' => $totalCost,
            'harvest_date' => $request->harvest_date,
            'expiry_date' => $request->expiry_date,
            'quality_grade' => $request->quality_grade,
            'temperature' => $request->temperature,
            'ph_level' => $request->ph_level,
            'fat_content' => $request->fat_content,
            'protein_content' => $request->protein_content,
            'status' => $request->status,
            'quality_notes' => $request->quality_notes,
        ]);

        return response()->json(['success' => true, 'raw_material' => $rawMaterial]);
    }

    /**
     * Update an existing raw material for the authenticated supplier.
     */
    public function updateRawMaterial(Request $request, $id)
    {
        $request->validate([
            'material_name' => 'required|string|max:255',
            'material_type' => 'required|in:milk,sugar,fruit',
            'material_code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'harvest_date' => 'required|date',
            'expiry_date' => 'required|date',
            'quality_grade' => 'required|in:A,B,C,D',
            'temperature' => 'nullable|numeric',
            'ph_level' => 'nullable|numeric|min:0|max:14',
            'fat_content' => 'nullable|numeric|min:0|max:100',
            'protein_content' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:available,in_use,expired,disposed',
            'quality_notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }
        $dairyFarm = \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return response()->json(['success' => false, 'message' => 'Dairy farm not found.'], 404);
        }
        $rawMaterial = \App\Models\RawMaterial::where('id', $id)
            ->where('dairy_farm_id', $dairyFarm->id)
            ->first();
        if (!$rawMaterial) {
            return response()->json(['success' => false, 'message' => 'Raw material not found.'], 404);
        }

        // Calculate total cost
        $totalCost = $request->quantity * $request->unit_price;

        $rawMaterial->update([
            'material_name' => $request->material_name,
            'material_type' => $request->material_type,
            'material_code' => $request->material_code,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_of_measure' => $request->unit_of_measure,
            'unit_price' => $request->unit_price,
            'total_cost' => $totalCost,
            'harvest_date' => $request->harvest_date,
            'expiry_date' => $request->expiry_date,
            'quality_grade' => $request->quality_grade,
            'temperature' => $request->temperature,
            'ph_level' => $request->ph_level,
            'fat_content' => $request->fat_content,
            'protein_content' => $request->protein_content,
            'status' => $request->status,
            'quality_notes' => $request->quality_notes,
        ]);

        return response()->json(['success' => true, 'raw_material' => $rawMaterial]);
    }

    /**
     * Store a new raw material from the Blade form (not API).
     */
    public function storeRawMaterialBlade(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string|max:255',
            'material_type' => 'required|in:milk,sugar,fruit',
            'material_code' => 'required|string|max:255|unique:raw_materials,material_code',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'harvest_date' => 'required|date',
            'expiry_date' => 'required|date',
            'quality_grade' => 'required|in:A,B,C,D',
            'temperature' => 'nullable|numeric',
            'ph_level' => 'nullable|numeric|min:0|max:14',
            'fat_content' => 'nullable|numeric|min:0|max:100',
            'protein_content' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:available,in_use,expired,disposed',
            'quality_notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $supplier = $user->supplier ?? null;
        if (!$supplier) {
            return back()->withErrors(['Supplier not found.']);
        }
        $dairyFarm = \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first();
        if (!$dairyFarm) {
            return back()->withErrors(['Dairy farm not found.']);
        }

        // Calculate total cost
        $totalCost = $request->quantity * $request->unit_price;

        \App\Models\RawMaterial::create([
            'dairy_farm_id' => $dairyFarm->id,
            'material_name' => $request->material_name,
            'material_type' => $request->material_type,
            'material_code' => $request->material_code,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_of_measure' => $request->unit_of_measure,
            'unit_price' => $request->unit_price,
            'total_cost' => $totalCost,
            'harvest_date' => $request->harvest_date,
            'expiry_date' => $request->expiry_date,
            'quality_grade' => $request->quality_grade,
            'temperature' => $request->temperature,
            'ph_level' => $request->ph_level,
            'fat_content' => $request->fat_content,
            'protein_content' => $request->protein_content,
            'status' => $request->status,
            'quality_notes' => $request->quality_notes,
        ]);

        return redirect()->route('supplier.raw-material-inventory')->with('success', 'Raw material added successfully!');
    }

    /**
     * Show the supplier profile and dairy farm info page.
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $supplier = $user->supplier;
        $dairyFarm = $supplier ? \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first() : null;
        return view('supplier.supplier-profile', compact('supplier', 'dairyFarm'));
    }

    /**
     * Update the supplier and dairy farm records.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $supplier = $user->supplier;
        $dairyFarm = $supplier ? \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first() : null;
        $request->validate([
            'company_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'business_address' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'farm_name' => 'required|string|max:255',
            'farm_code' => 'required|string|max:255',
            'farm_address' => 'required|string|max:255',
            'farm_phone' => 'required|string|max:255',
            'farm_email' => 'required|email|max:255',
            'farm_manager' => 'required|string|max:255',
            'manager_phone' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
        ]);
        if ($supplier) {
            $supplier->update($request->only([
                'company_name', 'registration_number', 'business_address', 'contact_person', 'contact_phone', 'contact_email',
            ]));
        }
        if ($dairyFarm) {
            $dairyFarm->update($request->only([
                'farm_name', 'farm_code', 'farm_address', 'farm_phone', 'farm_email', 'farm_manager', 'manager_phone', 'manager_email',
            ]));
        }
        return redirect()->route('supplier.raw-material-inventory')->with('success', 'Profile updated successfully!');
    }

    public function supplierDashboard()
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        // Total raw materials supplied
        $totalSupplied = \App\Models\RawMaterial::whereHas('dairyFarm', function($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        })->count();

        // Pending deliveries (orders)
        $pendingDeliveries = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        // Delivered batches (orders)
        $deliveredBatches = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)
            ->where('status', 'delivered')
            ->count();

        // Current inventory by type
        $inventory = \App\Models\RawMaterial::whereHas('dairyFarm', function($q) use ($supplier) {
            $q->where('supplier_id', $supplier->id);
        })
        ->where('status', 'available')
        ->selectRaw('material_type, SUM(quantity) as total, unit_of_measure')
        ->groupBy('material_type', 'unit_of_measure')
        ->get();

        $inventorySummary = [
            'milk' => ['qty' => 0, 'unit' => 'L'],
            'sugar' => ['qty' => 0, 'unit' => 'kg'],
            'fruit' => ['qty' => 0, 'unit' => 'kg'],
        ];
        foreach ($inventory as $item) {
            $type = strtolower($item->material_type);
            if (isset($inventorySummary[$type])) {
                $inventorySummary[$type]['qty'] = $item->total;
                $inventorySummary[$type]['unit'] = $item->unit_of_measure;
            }
        }

        return view('dashboard-supplier', compact(
            'totalSupplied',
            'pendingDeliveries',
            'deliveredBatches',
            'inventorySummary'
        ));
    }
}
