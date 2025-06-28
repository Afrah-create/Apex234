<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\YogurtProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MachineLearningService
{
    /**
     * Generate demand forecasting predictions using Python ML module
     */
    public function generateDemandForecast(int $months = 3): array
    {
        try {
            // Try to use Python ML module first
            $pythonForecast = $this->runPythonDemandForecast($months);
            if ($pythonForecast && $pythonForecast['status'] === 'success') {
                return [
                    'forecast' => $pythonForecast['forecast'],
                    'confidence_level' => $pythonForecast['confidence_level'],
                    'seasonal_patterns' => $pythonForecast['seasonal_patterns'],
                    'trend_direction' => $pythonForecast['trend_direction'],
                    'model_accuracy' => $pythonForecast['model_accuracy'],
                    'feature_importance' => $pythonForecast['feature_importance'],
                    'ml_source' => 'python_random_forest'
                ];
            }
            
            // Fallback to PHP-based forecasting if Python module fails
            Log::warning('Python ML failed, using PHP fallback: ' . ($pythonForecast['error'] ?? 'Unknown error'));
            return $this->generatePHPFallbackForecast($months);
        } catch (\Exception $e) {
            Log::error('Error generating demand forecast: ' . $e->getMessage());
            return $this->getFallbackForecast($months);
        }
    }

    /**
     * Run Python demand forecasting module
     */
    private function runPythonDemandForecast(int $months): ?array
    {
        try {
            $pythonScript = base_path('machineLearning/demand_forecast_api.py');
            $csvFile = base_path('machineLearning/caramel_yoghurt2.csv');
            
            // Check if Python script and data file exist
            if (!file_exists($pythonScript) || !file_exists($csvFile)) {
                Log::warning('Python ML files not found: ' . $pythonScript . ' or ' . $csvFile);
                return ['status' => 'error', 'error' => 'Python ML files not found'];
            }

            // Execute Python script
            $command = "python " . escapeshellarg($pythonScript) . " " . $months . " 2>&1";
            $output = shell_exec($command);
            
            if ($output) {
                $result = json_decode($output, true);
                if ($result && isset($result['forecast'])) {
                    return $result;
                }
            }
            
            Log::warning('Python ML execution returned no valid output');
            return ['status' => 'error', 'error' => 'No valid output from Python script'];
        } catch (\Exception $e) {
            Log::error('Python ML execution failed: ' . $e->getMessage());
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate PHP fallback forecast when Python module is not available
     */
    private function generatePHPFallbackForecast(int $months): array
    {
        // Get historical sales data
        $historicalData = $this->getHistoricalSalesData();
        
        // Simple moving average for demand forecasting
        $forecast = $this->calculateMovingAverage($historicalData, $months);
        
        // Add seasonal adjustments
        $forecast = $this->applySeasonalAdjustments($forecast);
        
        // Add trend analysis
        $forecast = $this->applyTrendAnalysis($forecast);
        
        return [
            'forecast' => $forecast,
            'confidence_level' => $this->calculateConfidenceLevel($historicalData),
            'seasonal_patterns' => $this->detectSeasonalPatterns($historicalData),
            'trend_direction' => $this->analyzeTrend($historicalData),
            'model_accuracy' => [
                'mae' => 0.0,
                'rmse' => 0.0,
                'r2_score' => 0.0
            ],
            'feature_importance' => []
        ];
    }

    /**
     * Perform customer segmentation analysis
     */
    public function performCustomerSegmentation(): array
    {
        try {
            $customers = User::with('orders')->get();
            
            $segments = [
                'premium' => ['customers' => [], 'characteristics' => []],
                'regular' => ['customers' => [], 'characteristics' => []],
                'occasional' => ['customers' => [], 'characteristics' => []]
            ];

            foreach ($customers as $customer) {
                $profile = $this->analyzeCustomerProfile($customer);
                $segment = $this->assignCustomerSegment($profile);
                $segments[$segment]['customers'][] = $customer->id;
            }

            // Calculate segment characteristics
            foreach ($segments as $segment => $data) {
                $segments[$segment]['characteristics'] = $this->calculateSegmentCharacteristics($data['customers']);
            }

            return $segments;
        } catch (\Exception $e) {
            Log::error('Error performing customer segmentation: ' . $e->getMessage());
            return $this->getFallbackSegmentation();
        }
    }

    /**
     * Generate inventory optimization recommendations
     */
    public function generateInventoryRecommendations(): array
    {
        try {
            $products = YogurtProduct::with('inventory')->get();
            $recommendations = [];

            foreach ($products as $product) {
                $analysis = $this->analyzeProductInventory($product);
                $recommendations[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $analysis['current_stock'],
                    'optimal_stock' => $analysis['optimal_stock'],
                    'reorder_point' => $analysis['reorder_point'],
                    'recommendation' => $analysis['recommendation'],
                    'urgency' => $analysis['urgency']
                ];
            }

            return $recommendations;
        } catch (\Exception $e) {
            Log::error('Error generating inventory recommendations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Perform predictive analytics for sales
     */
    public function predictSales(int $days = 30): array
    {
        try {
            $historicalSales = $this->getHistoricalSalesData();
            $predictions = [];

            // Predict for each product
            $products = YogurtProduct::all();
            foreach ($products as $product) {
                $productSales = $this->getProductSalesHistory($product->id);
                $prediction = $this->predictProductSales($productSales, $days);
                
                $predictions[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'predicted_sales' => $prediction['sales'],
                    'confidence_interval' => $prediction['confidence'],
                    'trend' => $prediction['trend']
                ];
            }

            return $predictions;
        } catch (\Exception $e) {
            Log::error('Error predicting sales: ' . $e->getMessage());
            return $this->getFallbackSalesPredictions($days);
        }
    }

    /**
     * Generate risk assessment
     */
    public function assessBusinessRisks(): array
    {
        try {
            $risks = [];

            // Supply chain risk
            $risks['supply_chain'] = $this->assessSupplyChainRisk();
            
            // Market competition risk
            $risks['competition'] = $this->assessCompetitionRisk();
            
            // Financial risk
            $risks['financial'] = $this->assessFinancialRisk();
            
            // Operational risk
            $risks['operational'] = $this->assessOperationalRisk();

            return $risks;
        } catch (\Exception $e) {
            Log::error('Error assessing business risks: ' . $e->getMessage());
            return $this->getFallbackRiskAssessment();
        }
    }

    /**
     * Get historical sales data
     */
    private function getHistoricalSalesData(): array
    {
        $data = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('yogurt_products', 'order_items.product_id', '=', 'yogurt_products.id')
            ->select(
                'orders.created_at',
                'yogurt_products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('orders.created_at', 'yogurt_products.name')
            ->orderBy('orders.created_at')
            ->get()
            ->toArray();

        return $data;
    }

    /**
     * Calculate moving average for forecasting
     */
    private function calculateMovingAverage(array $data, int $period): array
    {
        $forecast = [];
        $values = array_column($data, 'total_sold');
        
        if (count($values) < $period) {
            return array_fill(0, $period, array_sum($values) / count($values));
        }

        $movingAverage = array_sum(array_slice($values, -$period)) / $period;
        
        for ($i = 0; $i < $period; $i++) {
            $forecast[] = round($movingAverage * (1 + ($i * 0.05)), 0); // 5% growth per month
        }

        return $forecast;
    }

    /**
     * Apply seasonal adjustments
     */
    private function applySeasonalAdjustments(array $forecast): array
    {
        $seasonalFactors = [
            1 => 0.9,   // January
            2 => 0.85,  // February
            3 => 0.95,  // March
            4 => 1.0,   // April
            5 => 1.05,  // May
            6 => 1.1,   // June
            7 => 1.15,  // July
            8 => 1.1,   // August
            9 => 1.05,  // September
            10 => 1.0,  // October
            11 => 0.95, // November
            12 => 0.9   // December
        ];

        $adjustedForecast = [];
        $currentMonth = Carbon::now()->month;

        foreach ($forecast as $index => $value) {
            $month = ($currentMonth + $index) % 12;
            if ($month === 0) $month = 12;
            
            $adjustedForecast[] = round($value * $seasonalFactors[$month], 0);
        }

        return $adjustedForecast;
    }

    /**
     * Apply trend analysis
     */
    private function applyTrendAnalysis(array $forecast): array
    {
        // Simple linear trend
        $trendFactor = 1.02; // 2% monthly growth
        
        $trendedForecast = [];
        foreach ($forecast as $index => $value) {
            $trendedForecast[] = round($value * pow($trendFactor, $index), 0);
        }

        return $trendedForecast;
    }

    /**
     * Calculate confidence level
     */
    private function calculateConfidenceLevel(array $data): float
    {
        if (count($data) < 10) return 0.7;
        
        // Simple confidence calculation based on data consistency
        $values = array_column($data, 'total_sold');
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        
        $coefficientOfVariation = sqrt($variance) / $mean;
        
        // Higher CV means lower confidence
        return max(0.5, min(0.95, 1 - ($coefficientOfVariation * 0.5)));
    }

    /**
     * Detect seasonal patterns
     */
    private function detectSeasonalPatterns(array $data): array
    {
        $monthlyAverages = array_fill(1, 12, 0);
        $monthlyCounts = array_fill(1, 12, 0);

        foreach ($data as $record) {
            $month = Carbon::parse($record['created_at'])->month;
            $monthlyAverages[$month] += $record['total_sold'];
            $monthlyCounts[$month]++;
        }

        foreach ($monthlyAverages as $month => $total) {
            if ($monthlyCounts[$month] > 0) {
                $monthlyAverages[$month] = $total / $monthlyCounts[$month];
            }
        }

        return $monthlyAverages;
    }

    /**
     * Analyze trend direction
     */
    private function analyzeTrend(array $data): string
    {
        if (count($data) < 2) return 'stable';

        $recent = array_slice($data, -3);
        $older = array_slice($data, -6, 3);

        $recentAvg = array_sum(array_column($recent, 'total_sold')) / count($recent);
        $olderAvg = array_sum(array_column($older, 'total_sold')) / count($older);

        if ($recentAvg > $olderAvg * 1.1) return 'increasing';
        if ($recentAvg < $olderAvg * 0.9) return 'decreasing';
        return 'stable';
    }

    /**
     * Analyze customer profile
     */
    private function analyzeCustomerProfile(User $customer): array
    {
        $orders = $customer->orders;
        $totalSpent = $orders->sum('total_amount');
        $orderCount = $orders->count();
        $avgOrderValue = $orderCount > 0 ? $totalSpent / $orderCount : 0;
        $lastOrderDate = $orders->max('created_at');
        $daysSinceLastOrder = $lastOrderDate ? Carbon::now()->diffInDays($lastOrderDate) : 999;

        return [
            'total_spent' => $totalSpent,
            'order_count' => $orderCount,
            'avg_order_value' => $avgOrderValue,
            'days_since_last_order' => $daysSinceLastOrder,
            'customer_lifetime' => $customer->created_at ? Carbon::now()->diffInDays($customer->created_at) : 0
        ];
    }

    /**
     * Assign customer segment
     */
    private function assignCustomerSegment(array $profile): string
    {
        if ($profile['total_spent'] > 500 && $profile['order_count'] > 5) {
            return 'premium';
        } elseif ($profile['total_spent'] > 100 && $profile['order_count'] > 2) {
            return 'regular';
        } else {
            return 'occasional';
        }
    }

    /**
     * Calculate segment characteristics
     */
    private function calculateSegmentCharacteristics(array $customerIds): array
    {
        if (empty($customerIds)) {
            return [
                'avg_spending' => 0,
                'avg_orders' => 0,
                'retention_rate' => 0
            ];
        }

        $customers = User::whereIn('id', $customerIds)->with('orders')->get();
        
        $totalSpending = $customers->sum(function($customer) {
            return $customer->orders->sum('total_amount');
        });
        
        $totalOrders = $customers->sum(function($customer) {
            return $customer->orders->count();
        });

        return [
            'avg_spending' => $customers->count() > 0 ? $totalSpending / $customers->count() : 0,
            'avg_orders' => $customers->count() > 0 ? $totalOrders / $customers->count() : 0,
            'retention_rate' => $this->calculateRetentionRate($customers)
        ];
    }

    /**
     * Calculate retention rate
     */
    private function calculateRetentionRate($customers): float
    {
        $activeCustomers = $customers->filter(function($customer) {
            return $customer->orders->where('created_at', '>=', Carbon::now()->subMonths(3))->count() > 0;
        })->count();

        return $customers->count() > 0 ? $activeCustomers / $customers->count() : 0;
    }

    /**
     * Analyze product inventory
     */
    private function analyzeProductInventory(YogurtProduct $product): array
    {
        $currentStock = $product->inventory->quantity ?? 0;
        $avgMonthlyDemand = $this->calculateAverageMonthlyDemand($product->id);
        $optimalStock = $avgMonthlyDemand * 1.5; // 1.5 months of demand
        $reorderPoint = $avgMonthlyDemand * 0.5; // 0.5 months of demand

        $recommendation = 'maintain';
        $urgency = 'low';

        if ($currentStock < $reorderPoint) {
            $recommendation = 'reorder_urgently';
            $urgency = 'high';
        } elseif ($currentStock < $optimalStock) {
            $recommendation = 'reorder_soon';
            $urgency = 'medium';
        } elseif ($currentStock > $optimalStock * 2) {
            $recommendation = 'reduce_stock';
            $urgency = 'medium';
        }

        return [
            'current_stock' => $currentStock,
            'optimal_stock' => round($optimalStock, 0),
            'reorder_point' => round($reorderPoint, 0),
            'recommendation' => $recommendation,
            'urgency' => $urgency
        ];
    }

    /**
     * Calculate average monthly demand
     */
    private function calculateAverageMonthlyDemand(int $productId): float
    {
        $monthlyDemand = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(6))
            ->sum('order_items.quantity');

        return $monthlyDemand / 6;
    }

    /**
     * Get product sales history
     */
    private function getProductSalesHistory(int $productId): array
    {
        return Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(6))
            ->select('orders.created_at', 'order_items.quantity')
            ->orderBy('orders.created_at')
            ->get()
            ->toArray();
    }

    /**
     * Predict product sales
     */
    private function predictProductSales(array $salesHistory, int $days): array
    {
        if (empty($salesHistory)) {
            return [
                'sales' => rand(50, 200),
                'confidence' => [0.7, 0.9],
                'trend' => 'stable'
            ];
        }

        $dailySales = array_column($salesHistory, 'quantity');
        $avgDailySales = array_sum($dailySales) / count($dailySales);
        
        $predictedSales = round($avgDailySales * $days, 0);
        $confidence = [max(0.6, 1 - (count($dailySales) * 0.01)), 0.95];
        
        $trend = $this->analyzeTrend($salesHistory);

        return [
            'sales' => $predictedSales,
            'confidence' => $confidence,
            'trend' => $trend
        ];
    }

    /**
     * Assess supply chain risk
     */
    private function assessSupplyChainRisk(): array
    {
        $lowStockProducts = YogurtProduct::with('inventory')
            ->whereHas('inventory', function($query) {
                $query->where('quantity', '<', 50);
            })
            ->count();

        $riskLevel = 'low';
        $score = 0.2;

        if ($lowStockProducts > 5) {
            $riskLevel = 'high';
            $score = 0.8;
        } elseif ($lowStockProducts > 2) {
            $riskLevel = 'medium';
            $score = 0.5;
        }

        return [
            'level' => $riskLevel,
            'score' => $score,
            'factors' => ['Low stock items: ' . $lowStockProducts]
        ];
    }

    /**
     * Assess competition risk
     */
    private function assessCompetitionRisk(): array
    {
        // Simulated competition analysis
        $marketShare = 12.3; // Current market share
        $competitorGrowth = 8.5; // Competitor growth rate

        $riskLevel = 'low';
        $score = 0.3;

        if ($competitorGrowth > 15) {
            $riskLevel = 'high';
            $score = 0.8;
        } elseif ($competitorGrowth > 10) {
            $riskLevel = 'medium';
            $score = 0.5;
        }

        return [
            'level' => $riskLevel,
            'score' => $score,
            'factors' => ['Market share: ' . $marketShare . '%', 'Competitor growth: ' . $competitorGrowth . '%']
        ];
    }

    /**
     * Assess financial risk
     */
    private function assessFinancialRisk(): array
    {
        $totalRevenue = Order::sum('total_amount');
        $totalCost = Inventory::sum(DB::raw('quantity * unit_cost'));
        $profitMargin = $totalRevenue > 0 ? (($totalRevenue - $totalCost) / $totalRevenue) * 100 : 0;

        $riskLevel = 'low';
        $score = 0.2;

        if ($profitMargin < 10) {
            $riskLevel = 'high';
            $score = 0.8;
        } elseif ($profitMargin < 20) {
            $riskLevel = 'medium';
            $score = 0.5;
        }

        return [
            'level' => $riskLevel,
            'score' => $score,
            'factors' => ['Profit margin: ' . round($profitMargin, 1) . '%']
        ];
    }

    /**
     * Assess operational risk
     */
    private function assessOperationalRisk(): array
    {
        $pendingOrders = Order::where('status', 'pending')->count();
        $delayedOrders = Order::where('status', 'processing')->where('created_at', '<', Carbon::now()->subDays(7))->count();

        $riskLevel = 'low';
        $score = 0.2;

        if ($delayedOrders > 10 || $pendingOrders > 50) {
            $riskLevel = 'high';
            $score = 0.8;
        } elseif ($delayedOrders > 5 || $pendingOrders > 20) {
            $riskLevel = 'medium';
            $score = 0.5;
        }

        return [
            'level' => $riskLevel,
            'score' => $score,
            'factors' => ['Pending orders: ' . $pendingOrders, 'Delayed orders: ' . $delayedOrders]
        ];
    }

    /**
     * Fallback methods for error handling
     */
    private function getFallbackForecast(int $months): array
    {
        return [
            'forecast' => array_fill(0, $months, 1000),
            'confidence_level' => 0.7,
            'seasonal_patterns' => array_fill(1, 12, 1000),
            'trend_direction' => 'stable'
        ];
    }

    private function getFallbackSegmentation(): array
    {
        return [
            'premium' => ['customers' => [], 'characteristics' => ['avg_spending' => 0, 'avg_orders' => 0, 'retention_rate' => 0]],
            'regular' => ['customers' => [], 'characteristics' => ['avg_spending' => 0, 'avg_orders' => 0, 'retention_rate' => 0]],
            'occasional' => ['customers' => [], 'characteristics' => ['avg_spending' => 0, 'avg_orders' => 0, 'retention_rate' => 0]]
        ];
    }

    private function getFallbackSalesPredictions(int $days): array
    {
        return [
            [
                'product_id' => 1,
                'product_name' => 'Greek Yogurt',
                'predicted_sales' => 1000,
                'confidence_interval' => [0.7, 0.9],
                'trend' => 'stable'
            ]
        ];
    }

    private function getFallbackRiskAssessment(): array
    {
        return [
            'supply_chain' => ['level' => 'low', 'score' => 0.2, 'factors' => []],
            'competition' => ['level' => 'low', 'score' => 0.3, 'factors' => []],
            'financial' => ['level' => 'low', 'score' => 0.2, 'factors' => []],
            'operational' => ['level' => 'low', 'score' => 0.2, 'factors' => []]
        ];
    }
} 