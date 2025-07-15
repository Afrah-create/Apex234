<?php

namespace App\Http\Controllers;

use App\Models\DistributionCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDistributionCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centers = DistributionCenter::orderBy('center_name')->paginate(15);
        return view('admin.distribution-centers.index', compact('centers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only vendors not assigned to any distribution center
        $assignedVendorIds = DB::table('distribution_center_vendor')->pluck('vendor_id')->toArray();
        $vendors = \App\Models\Vendor::whereNotIn('id', $assignedVendorIds)->get();
        return view('admin.distribution-centers.create', compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'center_name' => 'required|string|max:255',
            'center_code' => 'required|string|max:50|unique:distribution_centers,center_code',
            'center_address' => 'required|string',
            'center_phone' => 'required|string|max:30',
            'center_email' => 'required|email|max:255',
            'center_manager' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:30',
            'manager_email' => 'required|email|max:255',
            'center_type' => 'required|string',
            'storage_capacity' => 'required|integer',
            'current_inventory' => 'required|integer',
            'temperature_control' => 'required|numeric',
            'humidity_control' => 'nullable|numeric',
            'delivery_vehicles' => 'required|integer',
            'delivery_radius' => 'required|integer',
            'facilities' => 'nullable|string',
            'certifications' => 'nullable|string',
            'certification_status' => 'required|string',
            'last_inspection_date' => 'nullable|date',
            'next_inspection_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        $validated['facilities'] = $request->facilities ? json_encode(explode(',', $request->facilities)) : null;
        $validated['certifications'] = $request->certifications ? json_encode(explode(',', $request->certifications)) : null;
        $vendorIds = $request->vendors ?? [];
        $alreadyAssigned = DB::table('distribution_center_vendor')
            ->whereIn('vendor_id', $vendorIds)
            ->exists();
        if ($alreadyAssigned) {
            return back()->withErrors(['One or more selected vendors are already assigned to another distribution center.']);
        }
        $center = DistributionCenter::create($validated);
        if ($request->has('vendors')) {
            $center->vendors()->sync($request->vendors);
        }
        return redirect()->route('admin.distribution-centers.index')->with('success', 'Distribution center created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $center = DistributionCenter::findOrFail($id);
        // Vendors not assigned to any center, or already assigned to this center
        $assignedVendorIds = DB::table('distribution_center_vendor')
            ->where('distribution_center_id', '!=', $center->id)
            ->pluck('vendor_id')
            ->toArray();
        $vendors = \App\Models\Vendor::whereNotIn('id', $assignedVendorIds)->get();
        return view('admin.distribution-centers.edit', compact('center', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $center = DistributionCenter::findOrFail($id);
        $validated = $request->validate([
            'center_name' => 'required|string|max:255',
            'center_code' => 'required|string|max:50|unique:distribution_centers,center_code,' . $center->id,
            'center_address' => 'required|string',
            'center_phone' => 'required|string|max:30',
            'center_email' => 'required|email|max:255',
            'center_manager' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:30',
            'manager_email' => 'required|email|max:255',
            'center_type' => 'required|string',
            'storage_capacity' => 'required|integer',
            'current_inventory' => 'required|integer',
            'temperature_control' => 'required|numeric',
            'humidity_control' => 'nullable|numeric',
            'delivery_vehicles' => 'required|integer',
            'delivery_radius' => 'required|integer',
            'facilities' => 'nullable|string',
            'certifications' => 'nullable|string',
            'certification_status' => 'required|string',
            'last_inspection_date' => 'nullable|date',
            'next_inspection_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        $validated['facilities'] = $request->facilities ? json_encode(explode(',', $request->facilities)) : null;
        $validated['certifications'] = $request->certifications ? json_encode(explode(',', $request->certifications)) : null;
        $vendorIds = $request->vendors ?? [];
        $alreadyAssigned = DB::table('distribution_center_vendor')
            ->whereIn('vendor_id', $vendorIds)
            ->where('distribution_center_id', '!=', $center->id)
            ->exists();
        if ($alreadyAssigned) {
            return back()->withErrors(['One or more selected vendors are already assigned to another distribution center.']);
        }
        $center->update($validated);
        if ($request->has('vendors')) {
            $center->vendors()->sync($request->vendors);
        }
        return redirect()->route('admin.distribution-centers.index')->with('success', 'Distribution center updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $center = DistributionCenter::findOrFail($id);
        $center->delete();
        return redirect()->route('admin.distribution-centers.index')->with('success', 'Distribution center deleted!');
    }

    /**
     * Get inventory statistics for all products at a distribution center (summed by product).
     */
    public function inventoryStats($id)
    {
        $center = \App\Models\DistributionCenter::findOrFail($id);
        $inventories = \App\Models\Inventory::where('distribution_center_id', $center->id)
            ->with('yogurtProduct')
            ->get();
        $productStats = [];
        foreach ($inventories as $inv) {
            $product = $inv->yogurtProduct;
            if ($product) {
                $pid = $product->id;
                if (!isset($productStats[$pid])) {
                    $productStats[$pid] = [
                        'product_id' => $product->id,
                        'product_name' => $product->product_name,
                        'available' => 0,
                        'reserved' => 0,
                        'damaged' => 0,
                        'expired' => 0,
                    ];
                }
                $productStats[$pid]['available'] += (float) $inv->quantity_available;
                $productStats[$pid]['reserved'] += (float) $inv->quantity_reserved;
                $productStats[$pid]['damaged'] += (float) $inv->quantity_damaged;
                $productStats[$pid]['expired'] += (float) $inv->quantity_expired;
            }
        }
        return response()->json([
            'distribution_center' => [
                'id' => $center->id,
                'center_name' => $center->center_name,
            ],
            'products' => array_values($productStats)
        ]);
    }
}
