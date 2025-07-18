<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\YogurtProduct;
use App\Models\User;

class VendorDashboardController extends Controller
{
    // Inventory summary for vendor
    public function inventorySummary(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        $vendorId = $vendor ? $vendor->id : null;

        // Product inventory summary for this vendor only
        $productSummary = \App\Models\Inventory::with(['yogurtProduct'])
            ->where('vendor_id', $vendorId)
            ->selectRaw('
                SUM(quantity_available) as total_available,
                SUM(quantity_reserved) as total_reserved,
                SUM(quantity_damaged) as total_damaged,
                SUM(quantity_expired) as total_expired,
                SUM(total_value) as total_value,
                COUNT(*) as total_batches
            ')
            ->first();

        // Raw materials summary for this vendor only
        $rawMaterialSummary = \DB::table('raw_materials')
            ->selectRaw('
                SUM(CASE WHEN status = "delivered" THEN quantity ELSE 0 END) as delivered_quantity,
                SUM(total_cost) as total_cost,
                COUNT(*) as total_materials
            ')
            ->where('vendor_id', $vendorId)
            ->where('status', 'delivered')
            ->first();

            return response()->json([
            'product_summary' => $productSummary,
            'raw_material_summary' => $rawMaterialSummary
        ]);
    }

    // Inventory chart data for vendor
    public function inventoryChart(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json([
                'labels' => [],
                'datasets' => []
            ]);
        }
        $inventoryData = \App\Models\Inventory::where('vendor_id', $vendor->id)
            ->join('yogurt_products', 'inventories.yogurt_product_id', '=', 'yogurt_products.id')
            ->select(
                'yogurt_products.product_name as product_name',
                DB::raw('SUM(inventories.quantity_available) as total_available'),
                DB::raw('SUM(inventories.quantity_reserved) as total_reserved'),
                DB::raw('SUM(inventories.quantity_damaged) as total_damaged'),
                DB::raw('SUM(inventories.quantity_expired) as total_expired')
            )
            ->groupBy('yogurt_products.id', 'yogurt_products.product_name')
            ->get();
        $chartData = [
            'labels' => $inventoryData->pluck('product_name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Available',
                    'data' => $inventoryData->pluck('total_available')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Reserved',
                    'data' => $inventoryData->pluck('total_reserved')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Damaged',
                    'data' => $inventoryData->pluck('total_damaged')->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Expired',
                    'data' => $inventoryData->pluck('total_expired')->toArray(),
                    'backgroundColor' => 'rgba(156, 163, 175, 0.8)',
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
        return response()->json($chartData);
    }

    // Order status summary for vendor
    public function orderStatus(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json([
                'pending' => 0,
                'confirmed' => 0,
                'shipped' => 0,
                'delivered' => 0,
            ]);
        }
        $productIds = YogurtProduct::pluck('id');
        $orders = \App\Models\Order::whereHas('orderItems.yogurtProduct', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->select('order_status', DB::raw('COUNT(*) as count'))
            ->groupBy('order_status')
            ->get();
        $statuses = [
            'pending' => 0,
            'confirmed' => 0,
            'shipped' => 0,
            'delivered' => 0,
        ];
        foreach ($orders as $order) {
            $statuses[$order->order_status] = $order->count;
        }
        return response()->json($statuses);
    }

    // Raw material statistics for vendor (all materials)
    public function rawMaterialStats(): JsonResponse
    {
        $vendorId = Auth::id();
        $materialTypes = ['milk', 'sugar', 'fruit'];
        $statuses = ['available', 'expired', 'disposed'];
        $result = [];
        foreach ($materialTypes as $type) {
            $result[ucfirst($type)] = [
                'available' => 0,
                'expired' => 0,
                'disposed' => 0,
            ];
        }
        $rows = DB::table('raw_materials')
            ->select('material_type', 'status', DB::raw('SUM(quantity) as total'))
            ->where('vendor_id', $vendorId)
            ->groupBy('material_type', 'status')
            ->get();
        foreach ($rows as $row) {
            $type = ucfirst($row->material_type);
            $status = $row->status;
            if (isset($result[$type][$status])) {
                $result[$type][$status] = (float) $row->total;
            }
        }
        return response()->json($result);
    }

    // Production summary for vendor
    public function productionSummary(): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return response()->json([
                'batches_produced' => 0,
                'units_produced' => 0,
                'units_sold' => 0,
                'units_inventory' => 0,
            ]);
        }
        $inventoryQuery = \App\Models\Inventory::where('vendor_id', $vendor->id);
        $batchesProduced = $inventoryQuery->count();
        $unitsProduced = $inventoryQuery->sum('quantity_available');
        $unitsSold = $inventoryQuery->sum('quantity_reserved');
        $unitsInInventory = $inventoryQuery->sum('quantity_available');
        return response()->json([
            'batches_produced' => $batchesProduced,
            'units_produced' => $unitsProduced,
            'units_sold' => $unitsSold,
            'units_inventory' => $unitsInInventory,
        ]);
    }

    // Show all deliveries for the logged-in vendor
    public function deliveries()
    {
        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            abort(403, 'No vendor profile found.');
        }
        $deliveries = \App\Models\Delivery::where('vendor_id', $vendor->id)
            ->orderByDesc('created_at')
            ->get();
        return view('vendor.deliveries', compact('deliveries'));
    }

    public function showDashboard()
    {
        $vendor = Auth::user()->vendor;
        $employees = $vendor ? $vendor->employees : collect();
        $lowStockNotifications = collect();
        if ($vendor) {
            // Low stock products
            $lowStockProducts = \App\Models\Inventory::with('yogurtProduct')
                ->where('quantity_available', '<=', 5)
                ->whereHas('yogurtProduct', function($q) use ($vendor) { $q->where('status', 'active')->where('vendor_id', $vendor->id); })
                ->get()
                ->map(function($inv) {
                    return [
                        'type' => 'product',
                        'name' => $inv->yogurtProduct->product_name ?? 'Product',
                        'quantity' => $inv->quantity_available,
                        'unit' => 'units',
                    ];
                });
            // Low stock raw materials
            $lowStockMaterials = \App\Models\RawMaterial::where('status', 'available')
                ->where('quantity', '<=', 5)
                ->get()
                ->map(function($rm) {
                    return [
                        'type' => 'raw_material',
                        'name' => $rm->material_name,
                        'quantity' => $rm->quantity,
                        'unit' => $rm->unit_of_measure,
                    ];
                });
            $lowStockNotifications = collect($lowStockProducts)->merge(collect($lowStockMaterials));
        }
        return view('dashboard-vendor', compact('vendor', 'employees', 'lowStockNotifications'));
    }

    public function saveInventoryStatusRanges(Request $request)
    {
        $request->validate([
            'warning_max' => 'required|integer|min:1',
            'low_max' => 'required|integer|gt:warning_max',
        ]);
        session(['inventory_warning_max' => $request->warning_max]);
        session(['inventory_low_max' => $request->low_max]);
        return back()->with('range_success', 'Inventory status ranges updated!');
    }

    public function myReports(Request $request)
    {
        $user = $request->user();
        $role = $user->getPrimaryRoleName();
        $userId = $user->id;
        $email = $user->email;

        $reports = \App\Models\ReportLog::whereHas('scheduledReport', function($q) use ($role, $userId) {
                $q->where('stakeholder_type', $role)
                  ->where('stakeholder_id', $userId);
            })
            ->orWhereJsonContains('recipients', $email)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $reports]);
    }

    public function reportsPage()
    {
        return view('vendor.my-reports');
    }
} 