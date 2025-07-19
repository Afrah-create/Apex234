<?php

namespace App\Http\Controllers;

use App\Models\Delivery;

class AdminDeliveryController extends Controller
{
    public function show(Delivery $delivery)
    {
        return view('admin.deliveries.show', compact('delivery'));
    }

    public function bulkUpdateStatus(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'delivery_ids' => 'required|array',
            'delivery_ids.*' => 'integer|exists:deliveries,id',
            'delivery_status' => 'required|in:scheduled,in_transit,out_for_delivery,delivered,failed,cancelled',
        ]);
        $count = \App\Models\Delivery::whereIn('id', $request->delivery_ids)
            ->update(['delivery_status' => $request->delivery_status]);
        return redirect()->back()->with('success', "$count deliveries updated successfully.");
    }

    public function driverDeliveryLoads()
    {
        // Get all drivers with their delivery counts
        $drivers = \App\Models\Driver::withCount(['deliveries' => function($query) {
            $query->whereIn('delivery_status', ['scheduled', 'in_transit', 'out_for_delivery', 'delivered']);
        }])->get();

        $labels = $drivers->map(function($driver) {
            return $driver->name ?? ("Driver #" . $driver->id);
        });
        $data = $drivers->map(function($driver) {
            return $driver->deliveries_count;
        });

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
} 