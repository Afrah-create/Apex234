<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use App\Notifications\DeliveryNoteNotification;
use Illuminate\Support\Facades\Storage;
use App\Models\Driver;
use App\Notifications\OrderStatusUpdate;

class DeliveryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'distribution_center_id' => 'required|exists:distribution_centers,id',
            'vendor_id' => 'required|exists:vendors,id',
            'vehicle_number' => 'nullable|string|max:50',
            'scheduled_delivery_date' => 'required|date',
            'scheduled_delivery_time' => 'required',
            'delivery_address' => 'required|string',
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
        ]);

        // Find the least busy driver (fewest active deliveries)
        $driver = Driver::withCount(['deliveries' => function($query) {
            $query->whereIn('delivery_status', ['scheduled', 'in_transit', 'out_for_delivery']);
        }])->orderBy('deliveries_count', 'asc')->first();

        // Fallback: if no driver found, return error
        if (!$driver) {
            return response()->json(['success' => false, 'message' => 'No available driver found.'], 422);
        }

        // Add driver details to validated data
        $validated['driver_id'] = $driver->id;
        $validated['driver_name'] = $driver->name;
        $validated['driver_phone'] = $driver->phone;
        $validated['driver_license'] = $driver->license;

        $delivery = Delivery::create(array_merge(
            $validated,
            [
                'delivery_number' => uniqid('DEL-'),
                'delivery_status' => 'scheduled',
            ]
        ));

        // Generate PDF and save to storage
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.delivery-note', ['delivery' => $delivery]);
        $pdfPath = 'delivery_notes/delivery_' . $delivery->id . '_' . time() . '.pdf';
        Storage::put('public/' . $pdfPath, $pdf->output());
        $delivery->pdf_path = $pdfPath;
        $delivery->save();

        // Send in-app notification to the vendor's user
        $vendor = \App\Models\Vendor::find($validated['vendor_id']);
        if ($vendor && $vendor->user) {
            $vendor->user->notify(new \App\Notifications\DeliveryNoteNotification($delivery));
            // Send email with PDF attached
            \Illuminate\Support\Facades\Mail::to($vendor->user->email)->send(
                new \App\Mail\DeliveryNoteMail($delivery, $pdf->output())
            );
        }

        // Notify the driver in their dashboard
        $order = \App\Models\Order::find($validated['order_id']);
        $oldStatus = $delivery->getOriginal('delivery_status') ?? 'scheduled';
        $newStatus = $delivery->delivery_status;
        $driver->notify(new OrderStatusUpdate($order, $oldStatus, $newStatus));

        // Notify the customer about the delivery
        if ($order && $order->customer) {
            $order->customer->notify(new \App\Notifications\DeliveryNoteNotification($delivery));
        }

        return response()->json(['success' => true, 'delivery' => $delivery]);
    }
} 