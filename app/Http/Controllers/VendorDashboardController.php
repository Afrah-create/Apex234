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

        // Get all inventories for this vendor
        $inventories = \App\Models\Inventory::with(['yogurtProduct'])
            ->where('vendor_id', $vendorId)
            ->get();

        $total_available = $inventories->sum('quantity_available');
        $total_reserved = $inventories->sum('quantity_reserved');
        $total_damaged = $inventories->sum('quantity_damaged');
        $total_expired = $inventories->sum('quantity_expired');
        $total_value = $inventories->sum('total_value');
        $total_products = $inventories->pluck('yogurt_product_id')->unique()->count();
        $low_stock_items = $inventories->filter(function($inv) { return $inv->quantity_available <= 10; })->count();

        return response()->json([
            'total_available' => $total_available,
            'total_reserved' => $total_reserved,
            'total_damaged' => $total_damaged,
            'total_expired' => $total_expired,
            'total_value' => $total_value,
            'total_products' => $total_products,
            'low_stock_items' => $low_stock_items,
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
        $vendorId = $vendor->id;
        $inventories = \App\Models\Inventory::with(['yogurtProduct'])
            ->where('vendor_id', $vendorId)
            ->get();
        $labels = $inventories->pluck('yogurtProduct.product_name')->unique()->values();
        $available = [];
        $reserved = [];
        $damaged = [];
        $expired = [];
        foreach ($labels as $productName) {
            $productInventories = $inventories->filter(function($inv) use ($productName) {
                return $inv->yogurtProduct->product_name === $productName;
            });
            $available[] = $productInventories->sum('quantity_available');
            $reserved[] = $productInventories->sum('quantity_reserved');
            $damaged[] = $productInventories->sum('quantity_damaged');
            $expired[] = $productInventories->sum('quantity_expired');
        }
        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Available',
                    'data' => $available,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Reserved',
                    'data' => $reserved,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Damaged',
                    'data' => $damaged,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Expired',
                    'data' => $expired,
                    'backgroundColor' => 'rgba(156, 163, 175, 0.8)',
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                    'borderWidth' => 1
                ]
            ]
        ]);
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
        $unitsInInventory = $inventoryQuery->get()->sum(function($inv) {
            return $inv->quantity_available - $inv->quantity_reserved;
        });
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
        $deliveries = \App\Models\Delivery::with(['order.customer', 'order.orderItems.yogurtProduct', 'retailer'])
            ->where('vendor_id', $vendor->id)
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
                ->whereRaw('quantity_available - quantity_reserved <= 5')
                ->whereHas('yogurtProduct', function($q) use ($vendor) { $q->where('status', 'active')->where('vendor_id', $vendor->id); })
                ->get()
                ->map(function($inv) {
                    return [
                        'type' => 'product',
                        'name' => $inv->yogurtProduct->product_name ?? 'Product',
                        'quantity' => $inv->quantity_available - $inv->quantity_reserved,
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