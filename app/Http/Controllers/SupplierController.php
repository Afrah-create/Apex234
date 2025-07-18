<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\DairyFarm;
use Illuminate\Support\Facades\Auth;
use App\Models\Driver;

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

        if (!$supplier) {
            // Redirect to home or another safe route with an error message
            return redirect()->route('dashboard')->with('error', 'No supplier profile found for this user.');
        }

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

        // --- RECENT ACTIVITY AGGREGATION ---
        $recentActivities = collect();

        // 1. Recent Supplies (RawMaterial creation)
        $dairyFarm = \App\Models\DairyFarm::where('supplier_id', $supplier->id)->first();
        if ($dairyFarm) {
            $supplies = \App\Models\RawMaterial::where('dairy_farm_id', $dairyFarm->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function($supply) {
                    return [
                        'date' => $supply->created_at,
                        'type' => 'Supply',
                        'material' => $supply->material_name ?? ucfirst($supply->material_type),
                        'quantity' => $supply->quantity . ($supply->unit_of_measure ? $supply->unit_of_measure : ''),
                        'status' => ucfirst($supply->status),
                        'notes' => $supply->quality_notes ?? null,
                    ];
                });
            $recentActivities = $recentActivities->concat($supplies);
        }

        // 2. Recent Orders (RawMaterialOrder)
        $orders = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function($order) {
                return [
                    'date' => $order->created_at,
                    'type' => 'Order',
                    'material' => $order->material_name ?? ucfirst($order->material_type),
                    'quantity' => $order->quantity . ($order->unit_of_measure ? $order->unit_of_measure : ''),
                    'status' => ucfirst($order->status),
                    'notes' => $order->notes ?? null,
                ];
            });
        $recentActivities = $recentActivities->concat($orders);

        // 3. Recent Deliveries (Delivery)
        $orderIds = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)->pluck('id');
        if ($orderIds->count() > 0) {
            $deliveries = \App\Models\Delivery::whereIn('order_id', $orderIds)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function($delivery) {
                    return [
                        'date' => $delivery->created_at,
                        'type' => 'Delivery',
                        'material' => $delivery->material ?? 'N/A',
                        'quantity' => $delivery->quantity ?? '',
                        'status' => ucfirst($delivery->delivery_status ?? $delivery->status ?? 'Scheduled'),
                        'notes' => $delivery->recipient_name ? ('To: ' . $delivery->recipient_name) : null,
                    ];
                });
            $recentActivities = $recentActivities->concat($deliveries);
        }

        // 4. Inventory Events (Expired, Disposed, etc.)
        if ($dairyFarm) {
            $inventoryEvents = \App\Models\RawMaterial::where('dairy_farm_id', $dairyFarm->id)
                ->whereIn('status', ['expired', 'disposed'])
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->updated_at,
                        'type' => 'Inventory',
                        'material' => $item->material_name ?? ucfirst($item->material_type),
                        'quantity' => $item->quantity . ($item->unit_of_measure ? $item->unit_of_measure : ''),
                        'status' => ucfirst($item->status),
                        'notes' => $item->quality_notes ?? 'Batch event',
                    ];
                });
            $recentActivities = $recentActivities->concat($inventoryEvents);
        }

        // Sort all activities by date descending, take top 10
        $recentActivities = $recentActivities->sortByDesc('date')->take(10)->values();

        return view('dashboard-supplier', compact(
            'totalSupplied',
            'pendingDeliveries',
            'deliveredBatches',
            'inventorySummary',
            'recentActivities'
        ));
    }

    // Show driver management UI
    public function manageDrivers()
    {
        $supplier = Auth::user()->supplier;
        $drivers = $supplier ? $supplier->drivers : collect();
        return view('supplier.manage-drivers', compact('drivers'));
    }

    // Store a new driver
    public function storeDriver(Request $request)
    {
        $supplier = Auth::user()->supplier;
        if (!$supplier) return back()->with('error', 'Supplier not found.');
        if ($supplier->drivers()->count() >= 3) {
            return back()->with('error', 'You can only have up to 3 drivers.');
        }
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'license' => 'required|string|max:50|unique:drivers,license,NULL,id,supplier_id,' . $supplier->id,
            'license_expiry' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'vehicle_number' => 'nullable|string|max:50',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('drivers', 'public');
        }
        $supplier->drivers()->create($data);
        return back()->with('success', 'Driver added successfully.');
    }

    // Update an existing driver
    public function updateDriver(Request $request, $driverId)
    {
        $supplier = Auth::user()->supplier;
        $driver = $supplier ? $supplier->drivers()->findOrFail($driverId) : null;
        if (!$driver) return back()->with('error', 'Driver not found.');
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'license' => 'required|string|max:50|unique:drivers,license,' . $driver->id . ',id,supplier_id,' . $supplier->id,
            'license_expiry' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'vehicle_number' => 'nullable|string|max:50',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('drivers', 'public');
        }
        $driver->update($data);
        return back()->with('success', 'Driver updated successfully.');
    }

    // Delete a driver
    public function deleteDriver($driverId)
    {
        $supplier = Auth::user()->supplier;
        $driver = $supplier ? $supplier->drivers()->findOrFail($driverId) : null;
        if ($driver) $driver->delete();
        return back()->with('success', 'Driver deleted successfully.');
    }

    // Show all deliveries for the supplier (track deliveries page)
    public function trackDeliveries()
    {
        $supplier = Auth::user()->supplier;
        $deliveries = collect();
        if ($supplier) {
            // Find all raw material order IDs for this supplier
            $orderIds = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)->pluck('id');
            // Fetch deliveries where order_id matches any of these IDs
            if ($orderIds->count() > 0) {
                $deliveries = \App\Models\Delivery::whereIn('order_id', $orderIds)->orderByDesc('created_at')->get();
            }
        }
        return view('supplier.track-deliveries', compact('deliveries'));
    }

    /**
     * Update stock for a product by name (from supplier dashboard form)
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);
        $product = \App\Models\YogurtProduct::where('product_name', $request->product_name)->first();
        if (!$product) {
            return back()->withErrors(['product_name' => 'Product not found.']);
        }
        $product->stock = $request->quantity;
        $product->stock_input_date = now();
        $product->save();
        return back()->with('stock_success', 'Stock updated for ' . $product->product_name . '!');
    }
}
