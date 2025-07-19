<?php

namespace App\Http\Controllers;

use App\Models\Delivery;

class AdminDeliveryController extends Controller
{
    public function show(Delivery $delivery)
    {
        return view('admin.deliveries.show', compact('delivery'));
    }
} 