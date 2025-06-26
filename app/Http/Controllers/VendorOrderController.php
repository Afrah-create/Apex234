<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Order;

class VendorOrderController extends Controller
{
    // List all suppliers
    public function suppliers(): JsonResponse
    {
        $suppliers = Supplier::with('user')->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->name ?? 'Supplier',
            ];
        });
        return response()->json($suppliers);
    }

    // Place a new raw material order
    public function placeRawMaterialOrder(Request $request): JsonResponse
    {
        $request->validate([
            'material' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);
        $orderId = DB::table('raw_material_orders')->insertGetId([
            'vendor_id' => Auth::id(),
            'supplier_id' => $request->supplier_id,
            'material' => $request->material,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'order_id' => $orderId]);
    }

    // List vendor's raw material orders
    public function listRawMaterialOrders(): JsonResponse
    {
        $orders = DB::table('raw_material_orders')
            ->join('suppliers', 'raw_material_orders.supplier_id', '=', 'suppliers.id')
            ->join('users', 'suppliers.user_id', '=', 'users.id')
            ->select('raw_material_orders.*', 'users.name as supplier_name')
            ->where('raw_material_orders.vendor_id', Auth::id())
            ->orderByDesc('raw_material_orders.created_at')
            ->get();
        return response()->json($orders);
    }

    // List product orders from retailers
    public function listProductOrders(): JsonResponse
    {
        $orders = Order::with(['retailer.user', 'orderItems', 'orderItems.yogurtProduct'])
            ->where('order_type', '!=', 'raw_material')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'date' => $order->order_date,
                    'retailer' => $order->retailer->user->name ?? 'Retailer',
                    'items' => $order->orderItems->map(function($item) {
                        return [
                            'product' => $item->yogurtProduct->product_name ?? '',
                            'quantity' => $item->quantity,
                        ];
                    }),
                    'status' => $order->order_status,
                ];
            });
        return response()->json($orders);
    }

    // Confirm/mark as processed a retailer order
    public function confirmProductOrder($id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $order->order_status = 'confirmed';
        $order->save();
        return response()->json(['success' => true]);
    }
} 