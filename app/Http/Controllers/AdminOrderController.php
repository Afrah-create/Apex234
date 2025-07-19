<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Retailer;
use App\Models\DistributionCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminOrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function getOrdersData(): JsonResponse
    {
        $orders = Order::with(['retailer', 'distributionCenter'])
            ->select(
                'orders.*',
                'retailers.store_name as retailer_name',
                'distribution_centers.center_name',
                'users.name as customer_name'
            )
            ->leftJoin('retailers', 'orders.retailer_id', '=', 'retailers.id')
            ->leftJoin('distribution_centers', 'orders.distribution_center_id', '=', 'distribution_centers.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->orderBy('orders.order_date', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function getOrderStatistics(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'confirmed_orders' => Order::where('order_status', 'confirmed')->count(),
            'processing_orders' => Order::where('order_status', 'processing')->count(),
            'shipped_orders' => Order::where('order_status', 'shipped')->count(),
            'delivered_orders' => Order::where('order_status', 'delivered')->count(),
            'cancelled_orders' => Order::where('order_status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => Order::where('payment_status', 'pending')->sum('total_amount'),
        ];

        // Monthly order trends
        $monthlyOrders = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        return response()->json([
            'stats' => $stats,
            'monthly_trends' => $monthlyOrders
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['retailer', 'distributionCenter'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'actual_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    /**
     * Update the payment status of an order (admin action)
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);
        $order = \App\Models\Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();
        return redirect()->route('admin.orders.index')->with('success', 'Payment status updated successfully.');
    }

    /**
     * Bulk update order status and/or payment status for selected orders
     */
    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer|exists:orders,id',
            'order_status' => 'nullable|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);
        $update = [];
        if ($data['order_status']) {
            $update['order_status'] = $data['order_status'];
        }
        if ($data['payment_status']) {
            $update['payment_status'] = $data['payment_status'];
        }
        if (empty($update)) {
            return response()->json(['success' => false, 'message' => 'No fields to update.'], 400);
        }
        $count = Order::whereIn('id', $data['order_ids'])->update($update);
        return response()->json(['success' => true, 'updated' => $count]);
    }

    // Archive a raw material order (admin)
    public function archiveRawMaterialOrder($id)
    {
        $order = \App\Models\RawMaterialOrder::find($id);
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

    // Unarchive a raw material order (admin)
    public function unarchiveRawMaterialOrder($id)
    {
        $order = \App\Models\RawMaterialOrder::find($id);
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

    // API: Get all raw material orders with vendor and supplier names
    public function allRawMaterialOrders(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $query = \App\Models\RawMaterialOrder::with(['vendor', 'supplier.user'])
            ->orderByDesc('created_at');
        $total = $query->count();
        $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->get()->map(function($order) {
            return [
                'id' => $order->id,
                'vendor_name' => $order->vendor ? ($order->vendor->name ?? $order->vendor->email ?? '-') : '-',
                'supplier_name' => $order->supplier && $order->supplier->user ? ($order->supplier->user->name ?? '-') : '-',
                'material_type' => $order->material_type,
                'material_name' => $order->material_name,
                'quantity' => $order->quantity,
                'unit_of_measure' => $order->unit_of_measure,
                'status' => $order->status,
                'archived' => $order->archived,
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

    // Export all raw material orders as CSV
    public function exportRawMaterialOrdersCsv()
    {
        $orders = \App\Models\RawMaterialOrder::with(['vendor', 'supplier.user'])->orderByDesc('created_at')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="raw_material_orders.csv"',
        ];
        $columns = [
            'Order ID', 'Vendor', 'Supplier', 'Material', 'Quantity', 'Unit', 'Unit Price', 'Total Amount', 'Status', 'Order Date', 'Expected Delivery', 'Actual Delivery', 'Created At'
        ];
        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->vendor ? ($order->vendor->name ?? $order->vendor->email ?? '-') : '-',
                    $order->supplier && $order->supplier->user ? ($order->supplier->user->name ?? '-') : '-',
                    $order->material_name . ' (' . $order->material_type . ')',
                    $order->quantity,
                    $order->unit_of_measure,
                    $order->unit_price,
                    $order->total_amount,
                    $order->status,
                    $order->order_date ? $order->order_date->format('Y-m-d H:i:s') : '',
                    $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : '',
                    $order->actual_delivery_date ? $order->actual_delivery_date->format('Y-m-d') : '',
                    $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // Export all raw material orders as PDF
    public function exportRawMaterialOrdersPdf()
    {
        $orders = \App\Models\RawMaterialOrder::with(['vendor', 'supplier.user'])->orderByDesc('created_at')->get();
        $pdf = Pdf::loadView('admin.reports.raw_material_orders_pdf', [
            'orders' => $orders
        ])->setPaper('a4', 'landscape');
        return $pdf->download('raw_material_orders.pdf');
    }

    public function markAsShipped(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        DB::beginTransaction();
        try {
            // Assign the least busy active driver
            $driver = \App\Models\Driver::withCount(['deliveries' => function($query) {
                $query->whereIn('delivery_status', ['scheduled', 'in_transit', 'out_for_delivery']);
            }])->where('status', 'active')->orderBy('deliveries_count', 'asc')->first();
            if ($driver) {
                $order->driver_id = $driver->id;
            }
            $order->order_status = 'shipped';
            $order->save();
            // Create delivery if it does not exist, otherwise update
            $delivery = $order->delivery;
            if (!$delivery) {
                $delivery = $order->delivery()->create([
                    'order_id' => $order->id,
                    'distribution_center_id' => $order->distribution_center_id,
                    'retailer_id' => $order->retailer_id ?? null,
                    'vendor_id' => $order->vendor_id ?? null,
                    'delivery_status' => 'scheduled',
                    'delivery_address' => $order->delivery_address,
                    'recipient_name' => $order->delivery_contact,
                    'recipient_phone' => $order->delivery_phone,
                    'delivery_number' => uniqid('DEL-'),
                    'driver_id' => $driver ? $driver->id : null,
                    'driver_name' => $driver ? $driver->name : null,
                    'driver_phone' => $driver ? $driver->phone : null,
                    'driver_license' => $driver ? $driver->license : null,
                    'vehicle_number' => $driver ? $driver->vehicle_number : null,
                    'scheduled_delivery_date' => $order->requested_delivery_date ?? now()->addDay(),
                    'scheduled_delivery_time' => '09:00',
                ]);
            } else {
                $delivery->driver_id = $driver ? $driver->id : null;
                $delivery->driver_name = $driver ? $driver->name : null;
                $delivery->driver_phone = $driver ? $driver->phone : null;
                $delivery->driver_license = $driver ? $driver->license : null;
                $delivery->vehicle_number = $driver ? $driver->vehicle_number : null;
                $delivery->delivery_status = 'scheduled';
                $delivery->save();
            }
            // Notify vendor and customer
            if ($order->vendor && $order->vendor->user) {
                $order->vendor->user->notify(new \App\Notifications\OrderStatusUpdate($order, 'confirmed', 'shipped'));
            }
            if ($order->customer) {
                $order->customer->notify(new \App\Notifications\OrderStatusUpdate($order, 'confirmed', 'shipped'));
            }
            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order marked as shipped and driver assigned.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.orders.index')->with('error', 'Failed to mark order as shipped: ' . $e->getMessage());
        }
    }
} 