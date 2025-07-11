<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Order;
use App\Models\RawMaterialOrder;
use App\Models\RawMaterial;

class VendorOrderController extends Controller
{
    // List all suppliers
    public function suppliers(): JsonResponse
    {
        $suppliers = Supplier::with('user')->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->name ?? 'Supplier',
                'email' => $s->user->email ?? '',
            ];
        });
        return response()->json($suppliers);
    }

    // Get available raw materials from suppliers
    public function availableRawMaterials(): JsonResponse
    {
        $materials = RawMaterial::with(['dairyFarm.supplier.user'])
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get()
            ->groupBy('material_type')
            ->map(function($items, $type) {
                // Group by supplier id (from dairyFarm relation)
                $suppliers = $items->groupBy(function($item) {
                    return $item->dairyFarm->supplier->id;
                })->map(function($supplierItems, $supplierId) {
                    $supplier = $supplierItems->first()->dairyFarm->supplier;
                    return [
                        'supplier_id' => $supplierId,
                        'supplier_name' => $supplier->user->name ?? 'Supplier',
                        'supplier_email' => $supplier->user->email ?? '',
                        'available_quantity' => $supplierItems->sum('quantity'),
                        'unit_price' => $supplierItems->avg('unit_price'),
                        'unit_of_measure' => $supplierItems->first()->unit_of_measure,
                        'batches' => $supplierItems->map(function($item) {
                            return [
                                'batch_code' => $item->material_code,
                                'quantity' => $item->quantity,
                                'unit_of_measure' => $item->unit_of_measure,
                                'expiry_date' => $item->expiry_date,
                            ];
                        })->values(),
                    ];
                })->values();
                return [
                    'type' => $type,
                    'total_quantity' => $items->sum('quantity'),
                    'suppliers' => $suppliers,
                ];
            });

        return response()->json($materials);
    }

    // Place a new raw material order with availability check
    public function placeRawMaterialOrder(Request $request): JsonResponse
    {
        $request->validate([
            'material_type' => 'required|string|in:milk,sugar,fruit',
            'material_name' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_of_measure' => 'required|string',
            'expected_delivery_date' => 'nullable|date|after:today',
        ]);

        $vendor = Auth::user();
        $supplier = Supplier::with('user')->findOrFail($request->supplier_id);

        // Check availability
        $availableMaterials = RawMaterial::whereHas('dairyFarm', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })
            ->where('material_type', $request->material_type)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get();

        $totalAvailable = $availableMaterials->sum('quantity');

        if ($totalAvailable < $request->quantity) {
            // Create order with unavailable status
            $order = RawMaterialOrder::create([
                'vendor_id' => $vendor->id,
                'supplier_id' => $supplier->id,
                'material_type' => $request->material_type,
                'material_name' => $request->material_name,
                'quantity' => $request->quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'unit_price' => $availableMaterials->avg('unit_price') ?? 0,
                'total_amount' => ($availableMaterials->avg('unit_price') ?? 0) * $request->quantity,
                'status' => 'unavailable',
                'notes' => "Insufficient inventory. Available: {$totalAvailable} {$request->unit_of_measure}, Requested: {$request->quantity} {$request->unit_of_measure}",
                'expected_delivery_date' => $request->expected_delivery_date,
            ]);

            return response()->json([
                'success' => false,
                'message' => "Insufficient inventory available. Only {$totalAvailable} {$request->unit_of_measure} available, but {$request->quantity} {$request->unit_of_measure} requested.",
                'order_id' => $order->id,
                'available_quantity' => $totalAvailable,
                'requested_quantity' => $request->quantity,
                'status' => 'unavailable'
            ], 400);
        }

        // Calculate total amount based on available materials
        $unitPrice = $availableMaterials->avg('unit_price') ?? 0;
        $totalAmount = $unitPrice * $request->quantity;

        // Create order with pending status
        $order = RawMaterialOrder::create([
            'vendor_id' => $vendor->id,
            'supplier_id' => $supplier->id,
            'material_type' => $request->material_type,
            'material_name' => $request->material_name,
            'quantity' => $request->quantity,
            'unit_of_measure' => $request->unit_of_measure,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'notes' => "Order placed successfully. Available inventory confirmed.",
            'expected_delivery_date' => $request->expected_delivery_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully! Supplier has been notified.',
            'order_id' => $order->id,
            'total_amount' => $totalAmount,
            'status' => 'pending'
        ]);
    }

    // List vendor's raw material orders
    public function listRawMaterialOrders(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $query = RawMaterialOrder::with(['supplier.user'])
            ->where('vendor_id', Auth::id())
            ->orderByDesc('created_at');
        $total = $query->count();
        $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->get()->map(function($order) {
            return [
                'id' => $order->id,
                'material_type' => $order->material_type,
                'material_name' => $order->material_name,
                'quantity' => $order->quantity,
                'unit_of_measure' => $order->unit_of_measure,
                'unit_price' => $order->unit_price,
                'total_amount' => $order->total_amount,
                'supplier_name' => $order->supplier->user->name ?? 'Supplier',
                'supplier_email' => $order->supplier->user->email ?? '',
                'status' => $order->status,
                'notes' => $order->notes,
                'order_date' => $order->order_date->format('Y-m-d H:i:s'),
                'expected_delivery_date' => $order->expected_delivery_date?->format('Y-m-d'),
                'actual_delivery_date' => $order->actual_delivery_date?->format('Y-m-d'),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });
        return response()->json([
            'data' => $orders,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ]);
    }

    // Cancel a raw material order
    public function cancelRawMaterialOrder($id): JsonResponse
    {
        $order = RawMaterialOrder::where('vendor_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json(['success' => false, 'message' => 'Order cannot be cancelled in current status.'], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => $order->notes . ' [Cancelled by vendor]'
        ]);

        return response()->json(['success' => true, 'message' => 'Order cancelled successfully.']);
    }

    // Archive a raw material order
    public function archiveRawMaterialOrder($id): \Illuminate\Http\JsonResponse
    {
        $order = \App\Models\RawMaterialOrder::where('vendor_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if (!in_array($order->status, ['delivered', 'cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Only delivered or cancelled orders can be archived.'], 400);
        }

        $order->archived = true;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Order archived successfully.']);
    }

    // Unarchive a raw material order
    public function unarchiveRawMaterialOrder($id): \Illuminate\Http\JsonResponse
    {
        $order = \App\Models\RawMaterialOrder::where('vendor_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if (!$order->archived) {
            return response()->json(['success' => false, 'message' => 'Order is not archived.'], 400);
        }

        $order->archived = false;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Order unarchived successfully.']);
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