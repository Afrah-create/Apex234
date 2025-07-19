<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\RawMaterialOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;

class SupplierOrderController extends Controller
{
    // List incoming raw material orders for the supplier
    public function incomingOrders(Request $request): JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['orders' => []]);
        }

        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $query = RawMaterialOrder::with(['vendor'])
            ->where('supplier_id', $supplier->id)
            ->orderByDesc('created_at');
        $total = $query->count();
        $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->get()->map(function($order) {
            return [
                'id' => $order->id,
                'vendor_id' => $order->vendor_id,
                'vendor_name' => $order->vendor->name ?? 'Vendor',
                'vendor_email' => $order->vendor->email ?? '',
                'vendor_address' => $order->vendor->business_address ?? '',
                'vendor_phone' => $order->vendor->contact_phone ?? '',
                'material_type' => $order->material_type,
                'material_name' => $order->material_name,
                'quantity' => $order->quantity,
                'unit_of_measure' => $order->unit_of_measure,
                'unit_price' => $order->unit_price,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'notes' => $order->notes,
                'order_date' => $order->order_date->format('Y-m-d H:i:s'),
                'expected_delivery_date' => $order->expected_delivery_date?->format('Y-m-d'),
                'actual_delivery_date' => $order->actual_delivery_date?->format('Y-m-d'),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });
        return response()->json([
            'orders' => $orders,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ]);
    }

    // Confirm a raw material order
    public function confirmOrder($id): JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = RawMaterialOrder::where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order cannot be confirmed in current status.'], 400);
        }

        // Check if we still have enough inventory
        $availableMaterials = RawMaterial::whereHas('dairyFarm', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })
            ->where('material_type', $order->material_type)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get();

        $totalAvailable = $availableMaterials->sum('quantity');

        if ($totalAvailable < $order->quantity) {
            $order->update([
                'status' => 'unavailable',
                'notes' => $order->notes . ' [Insufficient inventory on confirmation]'
            ]);

            return response()->json([
                'success' => false,
                'message' => "Insufficient inventory. Only {$totalAvailable} {$order->unit_of_measure} available.",
                'available_quantity' => $totalAvailable,
                'requested_quantity' => $order->quantity
            ], 400);
        }

        $order->update([
            'status' => 'confirmed',
            'notes' => $order->notes . ' [Confirmed by supplier]'
        ]);

        return response()->json(['success' => true, 'message' => 'Order confirmed successfully.']);
    }

    // Process/ship a confirmed order
    public function processOrder($id): JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = RawMaterialOrder::where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order->status !== 'confirmed') {
            return response()->json(['success' => false, 'message' => 'Order must be confirmed before processing.'], 400);
        }

        $order->update([
            'status' => 'processing',
            'notes' => $order->notes . ' [Processing started]'
        ]);

        return response()->json(['success' => true, 'message' => 'Order processing started.']);
    }

    // Mark order as shipped
    public function shipOrder($id): JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = RawMaterialOrder::where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order->status !== 'processing') {
            return response()->json(['success' => false, 'message' => 'Order must be processing before shipping.'], 400);
        }

        $order->update([
            'status' => 'shipped',
            'notes' => $order->notes . ' [Shipped to vendor]'
        ]);

        return response()->json(['success' => true, 'message' => 'Order marked as shipped.']);
    }

    // Mark order as delivered
    public function deliverOrder($id): JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = RawMaterialOrder::where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if ($order->status !== 'shipped') {
            return response()->json(['success' => false, 'message' => 'Order must be shipped before marking as delivered.'], 400);
        }

        // Deduct from supplier's inventory (FIFO)
        $remaining = $order->quantity;
        $batches = RawMaterial::whereHas('dairyFarm', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })
            ->where('material_type', $order->material_type)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date') // FIFO: earliest expiry first
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $deduct = min($batch->quantity, $remaining);
            $batch->quantity -= $deduct;
            if ($batch->quantity == 0) {
                $batch->status = 'in_use'; // or another status to indicate depleted
            }
            $batch->save();
            $remaining -= $deduct;
        }

        // Add to vendor's inventory (create or increment)
        $vendorId = $order->vendor_id;
        $vendorInventory = \App\Models\RawMaterial::where('vendor_id', $vendorId)
            ->where('material_type', $order->material_type)
            ->where('unit_of_measure', $order->unit_of_measure)
            ->first();
        $unitPrice = $order->unit_price ?? 0;
        $totalCost = $unitPrice * $order->quantity;
        // Use the first supplier batch for field values
        $supplierBatch = $batches->first();
        if ($vendorInventory) {
            $vendorInventory->quantity += $order->quantity;
            $vendorInventory->unit_price = $unitPrice;
            $vendorInventory->total_cost = $vendorInventory->quantity * $unitPrice;
            $vendorInventory->status = 'available';
            if ($supplierBatch) {
                $vendorInventory->harvest_date = $supplierBatch->harvest_date;
                $vendorInventory->expiry_date = $supplierBatch->expiry_date;
                $vendorInventory->quality_grade = $supplierBatch->quality_grade;
                $vendorInventory->temperature = $supplierBatch->temperature;
                $vendorInventory->ph_level = $supplierBatch->ph_level;
                $vendorInventory->fat_content = $supplierBatch->fat_content;
                $vendorInventory->protein_content = $supplierBatch->protein_content;
                $vendorInventory->description = $supplierBatch->description;
                $vendorInventory->quality_notes = $supplierBatch->quality_notes;
            }
            $vendorInventory->save();
        } else {
            \App\Models\RawMaterial::create([
                'vendor_id' => $vendorId,
                'material_type' => $order->material_type,
                'material_name' => $order->material_name,
                'quantity' => $order->quantity,
                'unit_of_measure' => $order->unit_of_measure,
                'unit_price' => $unitPrice,
                'total_cost' => $totalCost,
                'harvest_date' => $supplierBatch ? $supplierBatch->harvest_date : now(),
                'expiry_date' => $supplierBatch ? $supplierBatch->expiry_date : now()->addMonth(),
                'quality_grade' => $supplierBatch ? $supplierBatch->quality_grade : 'A',
                'temperature' => $supplierBatch ? $supplierBatch->temperature : null,
                'ph_level' => $supplierBatch ? $supplierBatch->ph_level : null,
                'fat_content' => $supplierBatch ? $supplierBatch->fat_content : null,
                'protein_content' => $supplierBatch ? $supplierBatch->protein_content : null,
                'description' => $supplierBatch ? $supplierBatch->description : null,
                'quality_notes' => $supplierBatch ? $supplierBatch->quality_notes : null,
                'status' => 'available',
                'material_code' => uniqid('vendor_'),
            ]);
        }

        $order->update([
            'status' => 'delivered',
            'actual_delivery_date' => now(),
            'notes' => $order->notes . ' [Delivered to vendor]'
        ]);

        return response()->json(['success' => true, 'message' => 'Order marked as delivered and inventories updated.']);
    }

    // Reject/cancel an order
    public function rejectOrder($id, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = RawMaterialOrder::where('supplier_id', $supplier->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json(['success' => false, 'message' => 'Order cannot be rejected in current status.'], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => $order->notes . " [Rejected by supplier: {$request->reason}]"
        ]);

        return response()->json(['success' => true, 'message' => 'Order rejected successfully.']);
    }

    // Archive a raw material order
    public function archiveRawMaterialOrder($id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)
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
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }

        $order = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)
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

    // Get order statistics for supplier dashboard
    public function orderStats(): JsonResponse
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return response()->json([
                'total_orders' => 0,
                'pending_orders' => 0,
                'confirmed_orders' => 0,
                'processing_orders' => 0,
                'shipped_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'unavailable_orders' => 0,
            ]);
        }

        $stats = RawMaterialOrder::where('supplier_id', $supplier->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return response()->json([
            'total_orders' => array_sum($stats),
            'pending_orders' => $stats['pending'] ?? 0,
            'confirmed_orders' => $stats['confirmed'] ?? 0,
            'processing_orders' => $stats['processing'] ?? 0,
            'shipped_orders' => $stats['shipped'] ?? 0,
            'delivered_orders' => $stats['delivered'] ?? 0,
            'cancelled_orders' => $stats['cancelled'] ?? 0,
            'unavailable_orders' => $stats['unavailable'] ?? 0,
        ]);
    }

    /**
     * Bulk update status for multiple raw material orders (supplier)
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:raw_material_orders,id',
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,unavailable',
        ]);
        $user = Auth::user();
        $supplier = $user->supplier;
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found.'], 404);
        }
        $orders = \App\Models\RawMaterialOrder::where('supplier_id', $supplier->id)
            ->whereIn('id', $request->order_ids)
            ->get();
        if ($orders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid orders found for this supplier.'], 404);
        }
        // Optionally, check for valid status transitions here
        foreach ($orders as $order) {
            $order->status = $request->status;
            $order->save();
        }
        return response()->json(['success' => true, 'updated' => $orders->count()]);
    }
} 