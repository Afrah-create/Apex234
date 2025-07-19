<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverDashboardApiController extends Controller
{
    public function assignedDeliveries()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $deliveries = [];
        if ($employee) {
            $deliveries = \App\Models\Delivery::where('driver_id', $employee->id)
                ->latest()
                ->take(10)
                ->with(['order.customer', 'order.orderItems.yogurtProduct', 'retailer'])
                ->get();
        }
        return response()->json(['deliveries' => $deliveries]);
    }
} 