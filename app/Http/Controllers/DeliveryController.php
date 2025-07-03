<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Notifications\DeliveryNoteNotification;

class DeliveryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'distribution_center_id' => 'required|exists:distribution_centers,id',
            'vendor_id' => 'required|exists:vendors,id',
            'vehicle_number' => 'nullable|string|max:50',
            'driver_name' => 'required|string|max:100',
            'driver_phone' => 'required|string|max:20',
            'driver_license' => 'nullable|string|max:50',
            'scheduled_delivery_date' => 'required|date',
            'scheduled_delivery_time' => 'required',
            'delivery_address' => 'required|string',
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
        ]);

        $delivery = Delivery::create(array_merge(
            $validated,
            [
                'delivery_number' => uniqid('DEL-'),
                'delivery_status' => 'scheduled',
            ]
        ));

        // Send in-app notification to the vendor's user
        $vendor = \App\Models\Vendor::find($validated['vendor_id']);
        if ($vendor && $vendor->user) {
            $vendor->user->notify(new \App\Notifications\DeliveryNoteNotification($delivery));
            // Generate PDF and send email
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.delivery-note', ['delivery' => $delivery]);
            \Illuminate\Support\Facades\Mail::to($vendor->user->email)->send(
                new \App\Mail\DeliveryNoteMail($delivery, $pdf->output())
            );
        }

        return response()->json(['success' => true, 'delivery' => $delivery]);
    }
} 