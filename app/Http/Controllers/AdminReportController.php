<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Inventory;
use App\Models\YogurtProduct;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Vendor;
use App\Models\Retailer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Get available report templates
     */
    public function getReportTemplates(): JsonResponse
    {
        // Return an empty array to clear all report templates
        return response()->json([]);
    }

    /**
     * Generate custom report
     */
    public function generateCustomReport(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'filters' => 'nullable|array',
            'group_by' => 'nullable|string',
            'sort_by' => 'nullable|string',
            'sort_order' => 'nullable|in:asc,desc'
        ]);

        try {
            $reportData = $this->buildReportData(
                $request->report_type,
                $request->date_from,
                $request->date_to,
                $request->filters ?? [],
                $request->group_by,
                $request->sort_by,
                $request->sort_order ?? 'desc'
            );

            // Check if report has data
            $hasData = !empty($reportData['data']) && count($reportData['data']) > 0;
            
            if (!$hasData) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => [],
                        'summary' => [
                            'message' => 'No data found for the selected criteria',
                            'report_type' => $request->report_type,
                            'date_range' => $request->date_from . ' to ' . $request->date_to
                        ],
                        'chart_data' => [
                            'labels' => [],
                            'datasets' => []
                        ]
                    ],
                    'metadata' => [
                        'report_type' => $request->report_type,
                        'date_range' => $request->date_from . ' to ' . $request->date_to,
                        'generated_at' => now()->format('Y-m-d H:i:s'),
                        'total_records' => 0,
                        'has_data' => false
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $reportData,
                'metadata' => [
                    'report_type' => $request->report_type,
                    'date_range' => $request->date_from . ' to ' . $request->date_to,
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'total_records' => count($reportData['data'] ?? []),
                    'has_data' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build report data based on type and filters
     */
    private function buildReportData($reportType, $dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder)
    {
        switch ($reportType) {
            case 'sales_summary':
                return $this->generateSalesSummaryReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder);
            
            case 'inventory_status':
                return $this->generateInventoryStatusReport($filters, $groupBy, $sortBy, $sortOrder);
            
            case 'supplier_performance':
                return $this->generateSupplierPerformanceReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder);
            
            case 'financial_summary':
                return $this->generateFinancialSummaryReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder);
            
            case 'user_analysis':
                return $this->generateUserAnalysisReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder);
            
            case 'production_metrics':
                return $this->generateProductionMetricsReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder);
            
            default:
                throw new \Exception('Unknown report type: ' . $reportType);
        }
    }

    /**
     * Generate sales summary report
     */
    private function generateSalesSummaryReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder)
    {
        $query = Order::with(['retailer', 'distributionCenter'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->whereIn('order_status', $filters['status']);
        }

        if (!empty($filters['retailer_id'])) {
            $query->whereIn('retailer_id', $filters['retailer_id']);
        }

        // Group by logic
        if ($groupBy === 'daily') {
            $data = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->groupBy('date')
            ->orderBy($sortBy ?? 'date', $sortOrder)
            ->get();
        } elseif ($groupBy === 'retailer') {
            $data = $query->select(
                'retailers.store_name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_revenue'),
                DB::raw('AVG(orders.total_amount) as avg_order_value')
            )
            ->join('retailers', 'orders.retailer_id', '=', 'retailers.id')
            ->groupBy('retailers.id', 'retailers.store_name')
            ->orderBy($sortBy ?? 'total_revenue', $sortOrder)
            ->get();
        } else {
            $data = $query->orderBy($sortBy ?? 'created_at', $sortOrder)->get();
        }

        // Calculate summary statistics
        $summary = [
            'total_orders' => $data->sum('order_count') ?? $data->count(),
            'total_revenue' => $data->sum('total_revenue') ?? $data->sum('total_amount'),
            'avg_order_value' => $data->avg('avg_order_value') ?? $data->avg('total_amount'),
            'date_range' => $dateFrom . ' to ' . $dateTo
        ];

        return [
            'data' => $data,
            'summary' => $summary,
            'chart_data' => $this->prepareChartData($data, $groupBy)
        ];
    }

    /**
     * Generate inventory status report
     */
    private function generateInventoryStatusReport($filters, $groupBy, $sortBy, $sortOrder)
    {
        $query = Inventory::with(['yogurtProduct']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->whereIn('inventory_status', $filters['status']);
        }

        if (!empty($filters['product_id'])) {
            $query->whereIn('yogurt_product_id', $filters['product_id']);
        }

        // Group by logic
        if ($groupBy === 'product') {
            $data = $query->select(
                'yogurt_products.product_name',
                DB::raw('SUM(inventories.quantity_available) as total_available'),
                DB::raw('SUM(inventories.quantity_reserved) as total_reserved'),
                DB::raw('SUM(inventories.quantity_damaged) as total_damaged'),
                DB::raw('SUM(inventories.quantity_expired) as total_expired')
            )
            ->join('yogurt_products', 'inventories.yogurt_product_id', '=', 'yogurt_products.id')
            ->groupBy('yogurt_products.id', 'yogurt_products.product_name')
            ->orderBy($sortBy ?? 'total_available', $sortOrder)
            ->get();
        } else {
            $data = $query->orderBy($sortBy ?? 'created_at', $sortOrder)->get();
        }

        // Calculate summary statistics
        $summary = [
            'total_products' => $data->count(),
            'total_available' => $data->sum('total_available') ?? $data->sum('quantity_available'),
            'total_reserved' => $data->sum('total_reserved') ?? $data->sum('quantity_reserved'),
            'total_damaged' => $data->sum('total_damaged') ?? $data->sum('quantity_damaged'),
            'total_expired' => $data->sum('total_expired') ?? $data->sum('quantity_expired')
        ];

        return [
            'data' => $data,
            'summary' => $summary,
            'chart_data' => $this->prepareChartData($data, $groupBy)
        ];
    }

    /**
     * Generate supplier performance report
     */
    private function generateSupplierPerformanceReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder)
    {
        $query = Supplier::with(['user']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->whereIn('status', $filters['status']);
        }

        $data = $query->orderBy($sortBy ?? 'created_at', $sortOrder)->get();

        // Calculate performance metrics
        foreach ($data as $supplier) {
            $supplier->performance_metrics = [
                'total_orders' => 0, // Would need to implement order tracking
                'on_time_delivery' => 95, // Mock data
                'quality_rating' => 4.2, // Mock data
                'response_time' => '2.5 hours' // Mock data
            ];
        }

        $summary = [
            'total_suppliers' => $data->count(),
            'active_suppliers' => $data->where('status', 'active')->count(),
            'avg_quality_rating' => $data->avg('performance_metrics.quality_rating'),
            'avg_delivery_time' => '2.3 days' // Mock data
        ];

        return [
            'data' => $data,
            'summary' => $summary,
            'chart_data' => $this->prepareChartData($data, $groupBy)
        ];
    }

    /**
     * Generate financial summary report
     */
    private function generateFinancialSummaryReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder)
    {
        $query = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->whereIn('order_status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->whereIn('payment_status', $filters['payment_status']);
        }

        // Group by logic
        if ($groupBy === 'monthly') {
            $data = $query->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN total_amount ELSE 0 END) as paid_revenue'),
                DB::raw('SUM(CASE WHEN payment_status = "pending" THEN total_amount ELSE 0 END) as pending_revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy($sortBy ?? 'year', $sortOrder)
            ->orderBy('month', $sortOrder)
            ->get();
        } else {
            $data = $query->orderBy($sortBy ?? 'created_at', $sortOrder)->get();
        }

        // Calculate financial metrics
        $summary = [
            'total_revenue' => $data->sum('total_revenue') ?? $data->sum('total_amount'),
            'paid_revenue' => $data->sum('paid_revenue') ?? $data->where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => $data->sum('pending_revenue') ?? $data->where('payment_status', 'pending')->sum('total_amount'),
            'total_orders' => $data->sum('order_count') ?? $data->count(),
            'avg_order_value' => $data->avg('total_revenue') ?? $data->avg('total_amount'),
            'payment_rate' => $data->sum('paid_revenue') / max($data->sum('total_revenue'), 1) * 100
        ];

        return [
            'data' => $data,
            'summary' => $summary,
            'chart_data' => $this->prepareChartData($data, $groupBy)
        ];
    }

    /**
     * Generate user analysis report
     */
    private function generateUserAnalysisReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder)
    {
        $query = User::with(['roles'])
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['retailer', 'vendor', 'supplier']);
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply filters
        if (!empty($filters['role'])) {
            $query->whereHas('roles', function($q) use ($filters) {
                $q->whereIn('name', $filters['role']);
            });
        }

        // Note: is_active field might not exist in users table, so we'll skip this filter for now
        // if (!empty($filters['status'])) {
        //     $query->whereIn('is_active', $filters['status']);
        // }

        $data = $query->orderBy($sortBy ?? 'created_at', $sortOrder)->get();

        // Calculate role-specific metrics for each user
        foreach ($data as $user) {
            $primaryRole = $user->getPrimaryRoleName();
            $user->role_metrics = [];
            
            switch ($primaryRole) {
                case 'retailer':
                    // Find the retailer record associated with this user
                    $retailer = \App\Models\Retailer::where('user_id', $user->id)->first();
                    if ($retailer) {
                        $user->role_metrics = [
                            'total_orders' => Order::where('retailer_id', $retailer->id)->count(),
                            'total_spent' => Order::where('retailer_id', $retailer->id)->sum('total_amount'),
                            'last_order_date' => Order::where('retailer_id', $retailer->id)->max('created_at'),
                            'avg_order_value' => Order::where('retailer_id', $retailer->id)->avg('total_amount'),
                            'role_type' => 'Retailer'
                        ];
                    } else {
                        $user->role_metrics = [
                            'total_orders' => 0,
                            'total_spent' => 0,
                            'last_order_date' => null,
                            'avg_order_value' => 0,
                            'role_type' => 'Retailer'
                        ];
                    }
                    break;
                    
                case 'vendor':
                    // Use the users table directly for vendors
                    $vendor = \App\Models\User::where('id', $user->id)->where('role', 'vendor')->first();
                    if ($vendor) {
                        $user->role_metrics = [
                            'total_products' => YogurtProduct::count(),
                            'active_products' => YogurtProduct::where('is_active', true)->count(),
                            'total_inventory' => Inventory::whereHas('yogurtProduct', function($q) use ($vendor) {
                                $q->where('vendor_id', $vendor->id);
                            })->sum('quantity_available'),
                            'last_activity' => YogurtProduct::max('updated_at'),
                            'role_type' => 'Vendor'
                        ];
                    } else {
                        $user->role_metrics = [
                            'total_products' => 0,
                            'active_products' => 0,
                            'total_inventory' => 0,
                            'last_activity' => null,
                            'role_type' => 'Vendor'
                        ];
                    }
                    break;
                    
                case 'supplier':
                    // Find the supplier record associated with this user
                    $supplier = \App\Models\Supplier::where('user_id', $user->id)->first();
                    if ($supplier) {
                        $user->role_metrics = [
                            'total_raw_materials' => \App\Models\RawMaterial::where('supplier_id', $supplier->id)->count(),
                            'active_materials' => \App\Models\RawMaterial::where('supplier_id', $supplier->id)->count(),
                            'last_delivery' => \App\Models\RawMaterial::where('supplier_id', $supplier->id)->max('updated_at'),
                            'role_type' => 'Supplier'
                        ];
                    } else {
                        $user->role_metrics = [
                            'total_raw_materials' => 0,
                            'active_materials' => 0,
                            'last_delivery' => null,
                            'role_type' => 'Supplier'
                        ];
                    }
                    break;
                    
                default:
                    $user->role_metrics = [
                        'role_type' => 'Unknown'
                    ];
            }
        }

        // Calculate summary statistics
        $summary = [
            'total_users' => $data->count(),
            'active_users' => $data->count(), // Assuming all users are active since we don't have is_active field
            'new_users' => $data->where('created_at', '>=', now()->subDays(30))->count(),
            'retailers' => $data->where('role_metrics.role_type', 'Retailer')->count(),
            'vendors' => $data->where('role_metrics.role_type', 'Vendor')->count(),
            'suppliers' => $data->where('role_metrics.role_type', 'Supplier')->count(),
            'avg_retailer_value' => $data->where('role_metrics.role_type', 'Retailer')->avg('role_metrics.total_spent') ?? 0
        ];

        return [
            'data' => $data,
            'summary' => $summary,
            'chart_data' => $this->prepareUserChartData($data, $groupBy)
        ];
    }

    /**
     * Generate production metrics report
     */
    private function generateProductionMetricsReport($dateFrom, $dateTo, $filters, $groupBy, $sortBy, $sortOrder)
    {
        // Mock production data - in real implementation, you'd have production tables
        $data = collect([
            [
                'product_name' => 'Greek Vanilla Yoghurt',
                'production_volume' => 1500,
                'quality_score' => 95.2,
                'efficiency_rate' => 87.5,
                'defect_rate' => 2.1
            ],
            [
                'product_name' => 'Low Fat Blueberry Yoghurt',
                'production_volume' => 1200,
                'quality_score' => 92.8,
                'efficiency_rate' => 85.2,
                'defect_rate' => 3.4
            ],
            [
                'product_name' => 'Organic Strawberry Yoghurt',
                'production_volume' => 800,
                'quality_score' => 98.1,
                'efficiency_rate' => 91.3,
                'defect_rate' => 1.2
            ]
        ]);

        $summary = [
            'total_production' => $data->sum('production_volume'),
            'avg_quality_score' => $data->avg('quality_score'),
            'avg_efficiency_rate' => $data->avg('efficiency_rate'),
            'avg_defect_rate' => $data->avg('defect_rate')
        ];

        return [
            'data' => $data,
            'summary' => $summary,
            'chart_data' => $this->prepareChartData($data, $groupBy)
        ];
    }

    /**
     * Prepare chart data for user analysis visualization
     */
    private function prepareUserChartData($data, $groupBy)
    {
        if ($groupBy === 'role') {
            $roleData = [
                'Retailer' => $data->where('role_metrics.role_type', 'Retailer')->count(),
                'Vendor' => $data->where('role_metrics.role_type', 'Vendor')->count(),
                'Supplier' => $data->where('role_metrics.role_type', 'Supplier')->count()
            ];
            
            return [
                'labels' => array_keys($roleData),
                'datasets' => [
                    [
                        'label' => 'Users by Role',
                        'data' => array_values($roleData),
                        'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b']
                    ]
                ]
            ];
        }

        if ($groupBy === 'monthly') {
            $monthlyData = $data->groupBy(function($user) {
                return $user->created_at->format('Y-m');
            })->map(function($group) {
                return $group->count();
            });
            
            return [
                'labels' => $monthlyData->keys()->toArray(),
                'datasets' => [
                    [
                        'label' => 'New Users',
                        'data' => $monthlyData->values()->toArray(),
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)'
                    ]
                ]
            ];
        }

        // Default chart - user roles distribution
        $roleData = [
            'Retailer' => $data->where('role_metrics.role_type', 'Retailer')->count(),
            'Vendor' => $data->where('role_metrics.role_type', 'Vendor')->count(),
            'Supplier' => $data->where('role_metrics.role_type', 'Supplier')->count()
        ];
        
        return [
            'labels' => array_keys($roleData),
            'datasets' => [
                [
                    'label' => 'Users by Role',
                    'data' => array_values($roleData),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b']
                ]
            ]
        ];
    }

    /**
     * Prepare chart data for visualization
     */
    private function prepareChartData($data, $groupBy)
    {
        if ($groupBy === 'daily' || $groupBy === 'monthly') {
            return [
                'labels' => $data->pluck('date')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Revenue',
                        'data' => $data->pluck('total_revenue')->toArray(),
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)'
                    ]
                ]
            ];
        }

        return [
            'labels' => $data->pluck('product_name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Quantity',
                    'data' => $data->pluck('total_available')->toArray(),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
                ]
            ]
        ];
    }

    /**
     * Export report to different formats
     */
    public function exportReport(Request $request): JsonResponse
    {
        // If report_data is present, use it directly (for exporting current report from frontend)
        if ($request->has('report_data')) {
            $format = $request->input('format', 'pdf');
            $reportData = $request->input('report_data');

            // Use a dummy ScheduledReport for PDF generation
            $report = new \App\Models\ScheduledReport([
                'name' => 'Custom Export',
                'report_type' => 'custom',
                'format' => $format,
            ]);

            $reportService = app(\App\Services\ReportGenerationService::class);
            $pdfContent = $reportService->generatePdfContent($reportData, $report);

            $filename = 'custom_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $filepath = 'reports/' . $filename;
            Storage::disk('public')->put($filepath, $pdfContent);

            return response()->json([
                'success' => true,
                'download_url' => asset('storage/' . $filepath)
            ]);
        }

        $request->validate([
            'report_type' => 'required|string',
            'format' => 'required|in:pdf,excel,csv',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'filters' => 'nullable|array'
        ]);

        try {
            $reportData = $this->buildReportData(
                $request->report_type,
                $request->date_from,
                $request->date_to,
                $request->filters ?? [],
                null,
                null,
                'desc'
            );

            $filename = $request->report_type . '_' . date('Y-m-d_H-i-s') . '.' . $request->format;
            $filepath = 'reports/' . $filename;

            if ($request->format === 'pdf') {
                $reportService = app(\App\Services\ReportGenerationService::class);
                // Use user_analysis template for user analysis, generic for others
                $view = $request->report_type === 'user_analysis' ? 'admin.reports.user_analysis_pdf' : 'admin.reports.generic_report_pdf';
                $report = new \App\Models\ScheduledReport([
                    'name' => ucfirst(str_replace('_', ' ', $request->report_type)),
                    'report_type' => $request->report_type,
                    'format' => 'pdf',
                ]);
                $pdfContent = $reportService->generatePdfContent($reportData, $report, $view);
                Log::info('PDF content size: ' . strlen($pdfContent));
                Log::info('Attempting to save PDF to: ' . storage_path('app/public/reports/' . $filename));
                $written = Storage::disk('public')->put('reports/' . $filename, $pdfContent);
                Log::info('PDF save result: ' . ($written ? 'success' : 'failure'));
            } else {
                // Store report data as CSV/Excel/JSON as before
                Storage::disk('public')->put($filepath, json_encode($reportData));
            }

            return response()->json([
                'success' => true,
                'message' => 'Report exported successfully',
                'download_url' => asset('storage/' . $filepath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download exported report
     */
    public function downloadReport($filename)
    {
        $filepath = 'reports/' . $filename;
        
        if (!Storage::exists($filepath)) {
            abort(404, 'Report not found');
        }

        return Storage::download($filepath, $filename);
    }

    /**
     * Get report filters and options
     */
    public function getReportFilters(): JsonResponse
    {
        $filters = [
            'order_statuses' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'],
            'payment_statuses' => ['pending', 'paid', 'failed'],
            'inventory_statuses' => ['available', 'low_stock', 'out_of_stock', 'damaged', 'expired'],
            'user_roles' => ['retailer', 'supplier', 'vendor'],
            'date_ranges' => [
                ['label' => 'Last 7 days', 'value' => '7'],
                ['label' => 'Last 30 days', 'value' => '30'],
                ['label' => 'Last 90 days', 'value' => '90'],
                ['label' => 'This year', 'value' => 'year'],
                ['label' => 'Custom range', 'value' => 'custom']
            ],
            'group_by_options' => [
                ['label' => 'None', 'value' => ''],
                ['label' => 'Daily', 'value' => 'daily'],
                ['label' => 'Monthly', 'value' => 'monthly'],
                ['label' => 'Product', 'value' => 'product'],
                ['label' => 'Retailer', 'value' => 'retailer'],
                ['label' => 'Role', 'value' => 'role']
            ]
        ];

        return response()->json($filters);
    }

    public function salesReport(Request $request)
    {
        // Parse date filters
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->startOfMonth();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now();

        // Query total sales
        $totalSales = \App\Models\Order::where('order_status', 'delivered')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount');

        // Sales by product
        $salesByProduct = \App\Models\Order::where('order_status', 'delivered')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('orderItems.yogurtProduct')
            ->get()
            ->flatMap(function($order) {
                return $order->orderItems;
            })
            ->groupBy('yogurt_product_id')
            ->map(function($items) {
                $product = $items->first()->yogurtProduct;
                return [
                    'product_name' => $product ? $product->product_name : 'Unknown',
                    'total_sales' => $items->sum(function($item) { return $item->quantity * $item->unit_price; }),
                    'units_sold' => $items->sum('quantity'),
                ];
            })->values();

        // Sales by distribution center
        $salesByDistributionCenter = \App\Models\Order::where('order_status', 'delivered')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('distributionCenter')
            ->get()
            ->groupBy('distribution_center_id')
            ->map(function($orders, $centerId) {
                $center = $orders->first()->distributionCenter;
                $unitsSold = $orders->flatMap(function($order) {
                    return $order->orderItems;
                })->sum('quantity');
                return [
                    'center_name' => $center ? $center->center_name : 'Unknown',
                    'total_sales' => $orders->sum('total_amount'),
                    'units_sold' => $unitsSold,
                ];
            })->values();

        // Sales over time (daily or monthly)
        $interval = $dateFrom->diffInDays($dateTo) > 60 ? 'month' : 'day';
        $salesOverTime = \App\Models\Order::where('order_status', 'delivered')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw(
                $interval === 'month'
                    ? "DATE_FORMAT(created_at, '%Y-%m') as period, SUM(total_amount) as total_sales"
                    : "DATE(created_at) as period, SUM(total_amount) as total_sales"
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('admin.reports.sales', [
            'totalSales' => $totalSales,
            'salesByProduct' => $salesByProduct,
            'salesByDistributionCenter' => $salesByDistributionCenter,
            'salesOverTime' => $salesOverTime,
            'dateFrom' => $dateFrom->toDateString(),
            'dateTo' => $dateTo->toDateString(),
        ]);
    }

    public function productionReport(Request $request)
    {
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->startOfMonth();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now();

        // Total batches and units produced
        $totalBatches = \App\Models\ProductionBatch::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $totalUnits = \App\Models\ProductionBatch::whereBetween('created_at', [$dateFrom, $dateTo])->sum('quantity_produced');

        // Production by product
        $productionByProduct = \App\Models\ProductionBatch::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function($batches) {
                $product = $batches->first()->product;
                return [
                    'product_name' => $product ? $product->product_name : 'Unknown',
                    'batches' => $batches->count(),
                    'units_produced' => $batches->sum('quantity_produced'),
                ];
            })->values();

        // Production by vendor
        $productionByVendor = \App\Models\ProductionBatch::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('vendor')
            ->get()
            ->groupBy('vendor_id')
            ->map(function($batches) {
                $vendor = $batches->first()->vendor;
                return [
                    'vendor_name' => $vendor ? $vendor->business_name : 'Unknown',
                    'batches' => $batches->count(),
                    'units_produced' => $batches->sum('quantity_produced'),
                ];
            })->values();

        // Production over time (daily or monthly)
        $interval = $dateFrom->diffInDays($dateTo) > 60 ? 'month' : 'day';
        $productionOverTime = \App\Models\ProductionBatch::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw(
                $interval === 'month'
                    ? "DATE_FORMAT(created_at, '%Y-%m') as period, SUM(quantity_produced) as total_units"
                    : "DATE(created_at) as period, SUM(quantity_produced) as total_units"
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('admin.reports.production', [
            'totalBatches' => $totalBatches,
            'totalUnits' => $totalUnits,
            'productionByProduct' => $productionByProduct,
            'productionByVendor' => $productionByVendor,
            'productionOverTime' => $productionOverTime,
            'dateFrom' => $dateFrom->toDateString(),
            'dateTo' => $dateTo->toDateString(),
        ]);
    }

    public function inventoryReport(Request $request)
    {
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->startOfMonth();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now();

        // Total products and total stock
        $totalProducts = \App\Models\Inventory::distinct('yogurt_product_id')->count('yogurt_product_id');
        $totalStock = \App\Models\Inventory::sum('quantity_available');
        $lowStockItems = \App\Models\Inventory::where('quantity_available', '<=', 10)->count();

        // Inventory by product
        $inventoryByProduct = \App\Models\Inventory::with('yogurtProduct')
            ->get()
            ->groupBy('yogurt_product_id')
            ->map(function($items) {
                $product = $items->first()->yogurtProduct;
                return [
                    'product_name' => $product ? $product->product_name : 'Unknown',
                    'total_stock' => $items->sum('quantity_available'),
                    'low_stock' => $items->sum(function($item) { return $item->quantity_available <= 10 ? 1 : 0; }),
                ];
            })->values();

        // Inventory by distribution center
        $inventoryByCenter = \App\Models\Inventory::with('distributionCenter')
            ->get()
            ->groupBy('distribution_center_id')
            ->map(function($items) {
                $center = $items->first()->distributionCenter;
                return [
                    'center_name' => $center ? $center->center_name : 'Unknown',
                    'total_stock' => $items->sum('quantity_available'),
                    'low_stock' => $items->sum(function($item) { return $item->quantity_available <= 10 ? 1 : 0; }),
                ];
            })->values();

        return view('admin.reports.inventory', [
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'lowStockItems' => $lowStockItems,
            'inventoryByProduct' => $inventoryByProduct,
            'inventoryByCenter' => $inventoryByCenter,
            'dateFrom' => $dateFrom->toDateString(),
            'dateTo' => $dateTo->toDateString(),
        ]);
    }

    public function rawMaterialOrdersReport(Request $request)
    {
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->startOfMonth();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now();

        $orders = \App\Models\RawMaterialOrder::with(['supplier', 'vendor'])
            ->whereBetween('order_date', [$dateFrom, $dateTo])
            ->orderByDesc('order_date')
            ->get();

        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $deliveredOrders = $orders->where('status', 'delivered')->count();
        $totalSpend = $orders->sum('total_amount');

        // Prepare data for graph: total spend over time (by day)
        $ordersOverTime = $orders->groupBy(function($order) {
            return $order->order_date ? $order->order_date->format('Y-m-d') : 'Unknown';
        })->map(function($group) {
            return [
                'period' => $group->first()->order_date ? $group->first()->order_date->format('Y-m-d') : 'Unknown',
                'total_spend' => $group->sum('total_amount'),
                'order_count' => $group->count(),
            ];
        })->sortBy('period')->values();

        return view('admin.reports.raw_material_orders', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'deliveredOrders' => $deliveredOrders,
            'totalSpend' => $totalSpend,
            'dateFrom' => $dateFrom->toDateString(),
            'dateTo' => $dateTo->toDateString(),
            'ordersOverTime' => $ordersOverTime,
        ]);
    }
} 