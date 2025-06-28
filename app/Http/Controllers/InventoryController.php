<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\YogurtProduct;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory-analytics');
    }

    public function getInventoryChartData(): JsonResponse
    {
        // Get inventory data grouped by product with total quantities
        $inventoryData = Inventory::join('yogurt_products', 'inventories.yogurt_product_id', '=', 'yogurt_products.id')
            ->select(
                'yogurt_products.product_name as product_name',
                DB::raw('SUM(inventories.quantity_available) as total_available'),
                DB::raw('SUM(inventories.quantity_reserved) as total_reserved'),
                DB::raw('SUM(inventories.quantity_damaged) as total_damaged'),
                DB::raw('SUM(inventories.quantity_expired) as total_expired')
            )
            ->groupBy('yogurt_products.id', 'yogurt_products.product_name')
            ->get();

        // Format data for Chart.js
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

    public function getInventorySummary(): JsonResponse
    {
        $summary = [
            'total_products' => YogurtProduct::count(),
            'total_available' => Inventory::sum('quantity_available'),
            'total_reserved' => Inventory::sum('quantity_reserved'),
            'total_damaged' => Inventory::sum('quantity_damaged'),
            'total_expired' => Inventory::sum('quantity_expired'),
            'low_stock_items' => Inventory::where('inventory_status', 'low_stock')->count(),
            'out_of_stock_items' => Inventory::where('inventory_status', 'out_of_stock')->count(),
        ];

        return response()->json($summary);
    }

    public function getUserStatistics(): JsonResponse
    {
        // Get user count by role
        $userStats = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select(
                'roles.name as role_name',
                DB::raw('COUNT(*) as user_count')
            )
            ->groupBy('roles.id', 'roles.name')
            ->get();

        // Calculate total users
        $totalUsers = User::count();

        // Format data for pie chart
        $chartData = [
            'labels' => $userStats->pluck('role_name')->toArray(),
            'datasets' => [
                [
                    'data' => $userStats->pluck('user_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Blue for Admin
                        'rgba(34, 197, 94, 0.8)',    // Green for Vendor
                        'rgba(245, 158, 11, 0.8)',   // Orange for Retailer
                        'rgba(168, 85, 247, 0.8)',   // Purple for Supplier
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    'borderWidth' => 2
                ]
            ]
        ];

        // Add summary statistics
        $summary = [
            'total_users' => $totalUsers,
            'role_breakdown' => $userStats->map(function($role) use ($totalUsers) {
                return [
                    'role' => $role->role_name,
                    'count' => $role->user_count,
                    'percentage' => $totalUsers > 0 ? round(($role->user_count / $totalUsers) * 100, 1) : 0
                ];
            })
        ];

        return response()->json([
            'chart_data' => $chartData,
            'summary' => $summary
        ]);
    }
} 