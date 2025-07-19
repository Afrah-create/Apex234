<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Retailer;
use App\Models\YogurtProduct;

class RetailerOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'delivery_address' => 'required|string',
            'delivery_contact' => 'required|string',
            'delivery_phone' => 'required|string',
        ]);

        $user = Auth::user();
        $retailer = Retailer::where('user_id', $user->id)->first();
        if (!$retailer) {
            return response()->json(['success' => false, 'message' => 'Retailer not found.'], 422);
        }

        // Use default delivery details from retailer info
        $delivery_address = $retailer->store_address ?? 'Default Address';
        $delivery_contact = $retailer->store_manager ?? $user->name;
        $delivery_phone = $retailer->store_phone ?? '0000000000';

        DB::beginTransaction();
        try {
            $order = Order::create([
                'retailer_id' => $retailer->id,
                'distribution_center_id' => 1, // TODO: assign real distribution center
                'order_number' => 'ORD' . now()->format('YmdHis') . strtoupper(uniqid()),
                'order_date' => now(),
                'requested_delivery_date' => now()->addDays(2),
                'order_type' => 'regular',
                'order_status' => 'pending',
                'subtotal' => collect($request->cart)->sum(function($item) { return $item['price'] * $item['quantity']; }),
                'tax_amount' => 0,
                'shipping_cost' => 0,
                'discount_amount' => 0,
                'total_amount' => collect($request->cart)->sum(function($item) { return $item['price'] * $item['quantity']; }),
                'payment_method' => 'cash',
                'payment_status' => 'pending',
                'delivery_address' => $delivery_address,
                'delivery_contact' => $delivery_contact,
                'delivery_phone' => $delivery_phone,
                'special_instructions' => null,
                'notes' => null,
            ]);

            foreach ($request->cart as $item) {
                $product = YogurtProduct::find($item['id']);
                if (!$product) continue;
                OrderItem::create([
                    'order_id' => $order->id,
                    'yogurt_product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'discount_percentage' => 0,
                    'discount_amount' => 0,
                    'final_price' => $item['price'] * $item['quantity'],
                    'production_date' => now(), // Placeholder
                    'expiry_date' => now()->addDays(14), // Placeholder
                    'item_status' => 'pending',
                    'notes' => null,
                ]);
            }

            DB::commit();

            // --- Automatic Vendor Assignment ---
            $order->refresh();
            $orderItems = $order->orderItems()->with('yogurtProduct')->get();
            $eligibleVendors = \App\Models\Vendor::where('status', 'approved')->get()->filter(function($vendor) use ($orderItems) {
                foreach ($orderItems as $item) {
                    $product = $item->yogurtProduct;
                    if (!$product || $product->vendor_id != $vendor->id || $product->stock < $item->quantity) {
                        return false;
                    }
                }
                return true;
            });
            if ($eligibleVendors->isNotEmpty()) {
                $selectedVendor = $eligibleVendors->random();
                $order->vendor_id = $selectedVendor->id;
                $order->save();
                // Notify the vendor
                $selectedVendor->notify(new \App\Notifications\VendorAssignedOrderNotification($order));
            } else {
                // Optionally: handle no eligible vendor (e.g., notify admin, mark as unassigned, etc.)
            }
            // --- End Automatic Vendor Assignment ---

            // Notify all admins about the new order
            $admins = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'admin'); })->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\OrderPlacedNotification($order, $user));
            }

            return response()->json(['success' => true, 'order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()], 500);
        }
    }
} 