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

        // Notify supplier's user
        if ($supplier->user) {
            $supplier->user->notify(new \App\Notifications\RawMaterialOrderPlacedNotification($order, $vendor));
        }

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
        $vendor = \Illuminate\Support\Facades\Auth::user()->vendor;
        if (!$vendor) {
            return response()->json([]);
        }
        // Get all yogurt product IDs for which this vendor has inventory
        $vendorProductIds = \App\Models\Inventory::where('vendor_id', $vendor->id)
            ->pluck('yogurt_product_id')
            ->unique()
            ->toArray();
        // Get all orders (not raw material) that have at least one order item for these products
        $orders = \App\Models\Order::with(['retailer.user', 'orderItems.yogurtProduct'])
            ->where('order_type', '!=', 'raw_material')
            ->whereHas('orderItems', function($q) use ($vendorProductIds) {
                $q->whereIn('yogurt_product_id', $vendorProductIds);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'date' => $order->order_date,
                    'order_source' => $order->order_type === 'customer' ? 'Customer' : 'Retailer',
                    'retailer' => $order->retailer && $order->retailer->user ? $order->retailer->user->name : null,
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
    public function confirmProductOrder($id): \Illuminate\Http\JsonResponse
    {
        $order = \App\Models\Order::with('orderItems.yogurtProduct')->findOrFail($id);

        // Check inventory for each item
        foreach ($order->orderItems as $item) {
            $product = $item->yogurtProduct;
            if (!$product || $product->stock < $item->quantity) {
                // Mark order as failed/cancelled
                $order->order_status = 'cancelled';
                $order->notes = ($order->notes ?? '') . ' [Order failed: ' . ($product->product_name ?? 'Product') . ' out of stock]';
                $order->save();

                // Optionally, notify the retailer here

                return response()->json([
                    'success' => false,
                    'message' => 'Order failed: ' . ($product->product_name ?? 'Product') . ' out of stock'
                ], 400);
            }
        }

        // If all products are in stock, confirm the order
        $order->order_status = 'confirmed';
        $order->save();

        // Deduct inventory for each item (FIFO by expiry_date)
        foreach ($order->orderItems as $item) {
            $product = $item->yogurtProduct;
            $quantityToReserve = $item->quantity;
            $vendorId = $product->vendor_id;
            // Get all inventory records for this product and vendor, ordered by soonest expiry
            $inventories = \App\Models\Inventory::where('yogurt_product_id', $product->id)
                ->where('vendor_id', $vendorId)
                ->whereRaw('quantity_available > quantity_reserved') // Only inventory that has available stock
                ->orderBy('expiry_date')
                ->get();
            foreach ($inventories as $inventory) {
                if ($quantityToReserve <= 0) break;
                $availableForReservation = $inventory->quantity_available - $inventory->quantity_reserved;
                $reserve = min($availableForReservation, $quantityToReserve);
                $inventory->quantity_reserved += $reserve;
                $inventory->save();
                $quantityToReserve -= $reserve;
            }
            // Sync product stock with sum of all available inventory (available - reserved)
            $product->stock = $product->inventories()->get()->sum(function($inv) {
                return $inv->quantity_available - $inv->quantity_reserved;
            });
            $product->save();
        }

        return response()->json(['success' => true]);
    }

    public function assignDriver(Request $request, $orderId)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
        ]);

        $order = \App\Models\Order::findOrFail($orderId);

        // Ensure the driver belongs to the vendor (supplier_id == vendor id)
        $vendor = auth()->user()->vendor;
        $driver = \App\Models\Driver::where('id', $request->driver_id)
            ->where('supplier_id', $vendor->id)
            ->firstOrFail();

        $order->driver_id = $driver->id;
        $order->order_status = 'out_for_delivery';
        $order->save();

        // Optionally: notify the driver

        return back()->with('success', 'Driver assigned successfully!');
    }

    /**
     * Show the form for assigning drivers to orders (Vendor UI)
     */
    public function showAssignDriverForm()
    {
        $vendor = Auth::user()->vendor;
        $vendorId = $vendor->id;
        // Get all orders for this vendor that need driver assignment
        $orders = \App\Models\Order::whereHas('orderItems.yogurtProduct', function($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })
        ->whereNull('driver_id')
        ->where('order_status', 'confirmed')
        ->get();
        // Get all employees for this vendor with role 'Driver'
        $drivers = \App\Models\Employee::where('vendor_id', $vendorId)
            ->where('role', 'Driver')
            ->get();
        return view('vendor.assign-driver', compact('orders', 'drivers'));
    }
} 