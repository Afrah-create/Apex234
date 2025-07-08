<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Retailer;
use App\Models\DistributionCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
                'distribution_centers.center_name'
            )
            ->join('retailers', 'orders.retailer_id', '=', 'retailers.id')
            ->join('distribution_centers', 'orders.distribution_center_id', '=', 'distribution_centers.id')
            ->orderBy('orders.created_at', 'desc')
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

    public function edit($id)
    {
        $order = Order::with(['retailer', 'distributionCenter'])->findOrFail($id);
        $retailers = Retailer::all();
        $distributionCenters = DistributionCenter::all();
        
        return view('admin.orders.edit', compact('order', 'retailers', 'distributionCenters'));
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
} 