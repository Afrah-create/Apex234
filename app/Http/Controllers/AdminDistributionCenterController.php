<?php

namespace App\Http\Controllers;

use App\Models\DistributionCenter;
use Illuminate\Http\Request;

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
        return view('admin.distribution-centers.create');
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
        DistributionCenter::create($validated);
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
        return view('admin.distribution-centers.edit', compact('center'));
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
        $center->update($validated);
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
}
