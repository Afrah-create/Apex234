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
        $vendorId = Auth::id();
        $productIds = YogurtProduct::where('status', 'active')->pluck('id');
        $summary = [
            'total_products' => $productIds->count(),
            'total_available' => Inventory::whereIn('yogurt_product_id', $productIds)->sum('quantity_available'),
            'total_reserved' => Inventory::whereIn('yogurt_product_id', $productIds)->sum('quantity_reserved'),
            'total_damaged' => Inventory::whereIn('yogurt_product_id', $productIds)->sum('quantity_damaged'),
            'total_expired' => Inventory::whereIn('yogurt_product_id', $productIds)->sum('quantity_expired'),
            'low_stock_items' => Inventory::whereIn('yogurt_product_id', $productIds)->where('inventory_status', 'low_stock')->count(),
            'out_of_stock_items' => Inventory::whereIn('yogurt_product_id', $productIds)->where('inventory_status', 'out_of_stock')->count(),
        ];
        return response()->json($summary);
    }

    // Inventory chart data for vendor
    public function inventoryChart(): JsonResponse
    {
        $vendorId = Auth::id();
        $products = YogurtProduct::where('status', 'active')->get();
        $inventoryData = Inventory::whereIn('yogurt_product_id', $products->pluck('id'))
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
        $vendorId = Auth::id();
        $orders = Order::select('order_status', DB::raw('COUNT(*) as count'))
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
        $vendorId = auth()->id();
        $materialTypes = ['milk', 'sugar', 'fruit'];
        $result = [];
        foreach ($materialTypes as $type) {
            $stats = DB::table('raw_materials')
                ->where('material_type', $type)
                ->where('vendor_id', $vendorId)
            ->select(
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(in_use) as in_use'),
                    DB::raw('SUM(expired) as expired'),
                    DB::raw('SUM(disposed) as disposed')
            )->first();
            $result[ucfirst($type)] = [
                'available' => (int)($stats->available ?? 0),
                'in_use' => (int)($stats->in_use ?? 0),
                'expired' => (int)($stats->expired ?? 0),
                'disposed' => (int)($stats->disposed ?? 0),
            ];
        }
        return response()->json($result);
    }

    // Production summary for vendor
    public function productionSummary(): JsonResponse
    {
        $vendorId = Auth::id();
        $batchesProduced = DB::table('inventories')->count();
        $unitsProduced = DB::table('inventories')->sum('quantity_available');
        $unitsSold = DB::table('inventories')->sum('quantity_reserved');
        $unitsInInventory = DB::table('inventories')->sum('quantity_available');
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
        $vendor = auth()->user()->vendor;
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
        $vendor = auth()->user()->vendor;
        $employees = $vendor ? $vendor->employees : collect();
        $lowStockNotifications = collect();
        if ($vendor) {
            // Low stock products
            $lowStockProducts = \App\Models\Inventory::with('yogurtProduct')
                ->where('quantity_available', '<=', 5)
                ->whereHas('yogurtProduct', function($q) { $q->where('status', 'active'); })
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
            $lowStockMaterials = \App\Models\RawMaterial::where('vendor_id', $vendor->id)
                ->where('status', 'available')
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
            $lowStockNotifications = $lowStockProducts->merge($lowStockMaterials);
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
} 