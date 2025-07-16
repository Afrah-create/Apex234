<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DriverOrderController extends Controller
{
    public function uploadProof(Request $request, $orderId)
    {
        $request->validate([
            'proof_photo' => 'required|image|max:4096', // 4MB max
        ]);

        $order = Order::findOrFail($orderId);

        // Only allow the assigned driver to upload proof
        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee || !$employee->driver || $order->driver_id !== $employee->driver->id) {
            abort(403, 'Unauthorized');
        }

        // Store the photo
        $path = $request->file('proof_photo')->store('proof_photos', 'public');
        $order->proof_photo = $path;
        $order->order_status = 'delivered';
        $order->save();

        // Optionally: notify retailer, admin, etc.

        return redirect()->back()->with('success', 'Order marked as delivered and proof uploaded!');
    }
} 