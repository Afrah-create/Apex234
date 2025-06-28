<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\YogurtProduct;
use App\Models\User;
use App\Services\MachineLearningService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }

    /**
     * Display the analytics and reports dashboard
     */
    public function index()
    {
        try {
            return view('admin.analytics-reports');
        } catch (\Exception $e) {
            // Fallback to simple response if view fails
            return response()->json([
                'error' => 'View error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get KPI data for the dashboard
     */
    public function getKpiData(): JsonResponse
    {
        try {
            // Calculate revenue growth (comparing current month vs previous month)
            $currentMonthRevenue = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');

            $previousMonthRevenue = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('total_amount');

            $revenueGrowth = $previousMonthRevenue > 0 
                ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
                : 0;

            // Get order volume for current month
            $orderVolume = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // Calculate profit margin (simplified calculation)
            $totalRevenue = Order::sum('total_amount');
            $totalCost = Inventory::sum(DB::raw('quantity * unit_cost'));
            $profitMargin = $totalRevenue > 0 ? round((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : 0;

            // Customer satisfaction (simulated data)
            $customerSatisfaction = 94.2;

            return response()->json([
                'revenue_growth' => $revenueGrowth > 0 ? "+{$revenueGrowth}%" : "{$revenueGrowth}%",
                'order_volume' => number_format($orderVolume),
                'profit_margin' => "{$profitMargin}%",
                'customer_satisfaction' => "{$customerSatisfaction}%"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load KPI data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get machine learning predictions
     */
    public function getPredictions(): JsonResponse
    {
        try {
            // Use ML service for demand forecasting
            $demandForecast = $this->mlService->generateDemandForecast(3);
            
            // Get sales predictions
            $salesPredictions = $this->mlService->predictSales(30);

            return response()->json([
                'demand_forecast' => $demandForecast['forecast'],
                'sales_predictions' => $salesPredictions,
                'confidence_level' => round($demandForecast['confidence_level'] * 100, 0),
                'seasonal_patterns' => $demandForecast['seasonal_patterns'],
                'trend_direction' => $demandForecast['trend_direction']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load predictions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer segmentation data
     */
    public function getCustomerSegmentation(): JsonResponse
    {
        try {
            // Use ML service for customer segmentation
            $segmentation = $this->mlService->performCustomerSegmentation();
            
            $segments = [];
            $totalCustomers = 0;
            
            foreach ($segmentation as $segment => $data) {
                $customerCount = count($data['customers']);
                $totalCustomers += $customerCount;
                $segments[$segment] = $customerCount;
            }
            
            $percentages = [];
            foreach ($segments as $segment => $count) {
                $percentages[$segment] = $totalCustomers > 0 ? round(($count / $totalCustomers) * 100, 1) : 0;
            }

            return response()->json([
                'segments' => $percentages,
                'total_customers' => $totalCustomers,
                'characteristics' => array_map(function($data) {
                    return $data['characteristics'];
                }, $segmentation)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load customer segmentation',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inventory optimization recommendations
     */
    public function getInventoryOptimization(): JsonResponse
    {
        try {
            // Use ML service for inventory optimization
            $recommendations = $this->mlService->generateInventoryRecommendations();
            
            $lowStock = [];
            $reorderSuggested = [];
            $optimalStock = [];

            foreach ($recommendations as $rec) {
                if ($rec['urgency'] === 'high') {
                    $lowStock[] = $rec['product_name'];
                } elseif ($rec['urgency'] === 'medium') {
                    $reorderSuggested[] = $rec['product_name'];
                } else {
                    $optimalStock[] = $rec['product_name'];
                }
            }

            return response()->json([
                'low_stock' => $lowStock,
                'reorder_suggested' => $reorderSuggested,
                'optimal_stock' => $optimalStock,
                'detailed_recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load inventory optimization',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trend analysis data
     */
    public function getTrendAnalysis(): JsonResponse
    {
        try {
            $quarters = [];
            $revenue = [];
            $profit = [];

            for ($i = 3; $i >= 0; $i--) {
                $quarter = Carbon::now()->subQuarters($i);
                $quarterStart = $quarter->startOfQuarter();
                $quarterEnd = $quarter->endOfQuarter();

                $quarterRevenue = Order::whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->sum('total_amount');

                $quarterCost = Inventory::whereBetween('created_at', [$quarterStart, $quarterEnd])
                    ->sum(DB::raw('quantity * unit_cost'));

                $quarterProfit = $quarterRevenue - $quarterCost;

                $quarters[] = 'Q' . $quarter->quarter;
                $revenue[] = round($quarterRevenue, 0);
                $profit[] = round($quarterProfit, 0);
            }

            return response()->json([
                'quarters' => $quarters,
                'revenue' => $revenue,
                'profit' => $profit,
                'growth_rate' => $this->calculateGrowthRate($revenue),
                'market_share' => 12.3 // Simulated data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load trend analysis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        try {
            // Calculate Customer Acquisition Cost (simplified)
            $totalMarketingCost = 50000; // Simulated marketing budget
            $newCustomers = User::where('created_at', '>=', Carbon::now()->subMonth())->count();
            $cac = $newCustomers > 0 ? round($totalMarketingCost / $newCustomers, 2) : 0;

            // Calculate Customer Lifetime Value (simplified)
            $customers = User::with('orders')->get();
            $totalRevenue = $customers->sum(function($customer) {
                return $customer->orders->sum('total_amount');
            });
            $clv = $customers->count() > 0 ? round($totalRevenue / $customers->count(), 2) : 0;

            // Conversion rate (simplified)
            $conversionRate = 3.2; // Simulated data

            // Churn rate (simplified)
            $churnRate = 2.1; // Simulated data

            return response()->json([
                'customer_acquisition_cost' => $cac,
                'customer_lifetime_value' => $clv,
                'conversion_rate' => $conversionRate,
                'churn_rate' => $churnRate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load performance metrics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get risk assessment
     */
    public function getRiskAssessment(): JsonResponse
    {
        try {
            // Use ML service for risk assessment
            $risks = $this->mlService->assessBusinessRisks();
            
            return response()->json($risks);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load risk assessment',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run scenario analysis
     */
    public function runScenarioAnalysis(Request $request): JsonResponse
    {
        try {
            $scenario = $request->input('scenario', 'realistic');
            
            $scenarios = [
                'optimistic' => [
                    'growth' => '+20%',
                    'revenue' => 75000,
                    'risk' => 'Low',
                    'recommendations' => [
                        'Increase production capacity',
                        'Expand marketing budget',
                        'Launch new product lines'
                    ]
                ],
                'realistic' => [
                    'growth' => '+10%',
                    'revenue' => 65000,
                    'risk' => 'Medium',
                    'recommendations' => [
                        'Maintain current operations',
                        'Focus on customer retention',
                        'Optimize inventory levels'
                    ]
                ],
                'pessimistic' => [
                    'growth' => '-5%',
                    'revenue' => 55000,
                    'risk' => 'High',
                    'recommendations' => [
                        'Reduce operational costs',
                        'Focus on core products',
                        'Strengthen supplier relationships'
                    ]
                ]
            ];

            return response()->json($scenarios[$scenario] ?? $scenarios['realistic']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to run scenario analysis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run what-if analysis
     */
    public function runWhatIfAnalysis(Request $request): JsonResponse
    {
        try {
            $priceChange = $request->input('price_change', 0);
            $marketingBudget = $request->input('marketing_budget', 10000);

            // Simulate what-if analysis
            $baseRevenue = 60000;
            $priceImpact = $priceChange * 0.15; // 15% of price change affects revenue
            $marketingImpact = ($marketingBudget - 10000) * 0.02; // 2% return on marketing investment
            
            $predictedRevenue = $baseRevenue * (1 + ($priceImpact + $marketingImpact) / 100);
            $predictedProfit = $predictedRevenue * 0.25; // 25% profit margin

            return response()->json([
                'price_change' => $priceChange,
                'marketing_budget' => $marketingBudget,
                'predicted_revenue' => round($predictedRevenue, 2),
                'predicted_profit' => round($predictedProfit, 2),
                'impact_percentage' => round((($predictedRevenue - $baseRevenue) / $baseRevenue) * 100, 1)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to run what-if analysis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export analytics report
     */
    public function exportReport(Request $request): JsonResponse
    {
        try {
            $reportType = $request->input('type', 'sales');
            
            // Simulate report generation
            $reports = [
                'sales' => [
                    'type' => 'PDF',
                    'filename' => 'sales_report_' . date('Y-m-d') . '.pdf',
                    'data' => $this->generateSalesReport()
                ],
                'inventory' => [
                    'type' => 'Excel',
                    'filename' => 'inventory_report_' . date('Y-m-d') . '.xlsx',
                    'data' => $this->generateInventoryReport()
                ],
                'analytics' => [
                    'type' => 'PDF',
                    'filename' => 'analytics_report_' . date('Y-m-d') . '.pdf',
                    'data' => $this->generateAnalyticsReport()
                ],
                'ml' => [
                    'type' => 'JSON',
                    'filename' => 'ml_insights_' . date('Y-m-d') . '.json',
                    'data' => $this->generateMLReport()
                ]
            ];

            return response()->json($reports[$reportType] ?? $reports['sales']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate(array $values): float
    {
        if (count($values) < 2) return 0;
        
        $current = end($values);
        $previous = prev($values);
        
        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : 0;
    }

    /**
     * Generate sales report data
     */
    private function generateSalesReport(): array
    {
        return [
            'period' => 'Last 30 Days',
            'total_sales' => Order::where('created_at', '>=', Carbon::now()->subDays(30))->sum('total_amount'),
            'total_orders' => Order::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'top_products' => $this->getTopProducts()
        ];
    }

    /**
     * Generate inventory report data
     */
    private function generateInventoryReport(): array
    {
        return [
            'total_products' => YogurtProduct::count(),
            'low_stock_items' => $this->getLowStockItems(),
            'inventory_value' => Inventory::sum(DB::raw('quantity * unit_cost'))
        ];
    }

    /**
     * Generate analytics report data
     */
    private function generateAnalyticsReport(): array
    {
        return [
            'kpis' => $this->getKpiData()->getData(),
            'trends' => $this->getTrendAnalysis()->getData(),
            'performance' => $this->getPerformanceMetrics()->getData()
        ];
    }

    /**
     * Generate ML insights report
     */
    private function generateMLReport(): array
    {
        return [
            'predictions' => $this->getPredictions()->getData(),
            'segmentation' => $this->getCustomerSegmentation()->getData(),
            'optimization' => $this->getInventoryOptimization()->getData(),
            'risk_assessment' => $this->getRiskAssessment()->getData()
        ];
    }

    /**
     * Get top selling products
     */
    private function getTopProducts(): array
    {
        return Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('yogurt_products', 'order_items.product_id', '=', 'yogurt_products.id')
            ->select('yogurt_products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('yogurt_products.id', 'yogurt_products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get low stock items
     */
    private function getLowStockItems(): array
    {
        return YogurtProduct::with('inventory')
            ->whereHas('inventory', function($query) {
                $query->where('quantity', '<', 50);
            })
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'current_stock' => $product->inventory->quantity ?? 0,
                    'reorder_level' => 50
                ];
            })
            ->toArray();
    }

    /**
     * Simple test method for debugging
     */
    public function test()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Analytics controller is working'
        ]);
    }
} 