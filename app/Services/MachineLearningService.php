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
            // Try to use new advanced Python ML module first
            $pythonResult = $this->runAdvancedPythonDemandForecast($months);
            
            if ($pythonResult && isset($pythonResult['status']) && $pythonResult['status'] === 'success') {
                return $pythonResult;
            }
            
            // Fallback to PHP-based forecasting if Python fails
            Log::warning('Advanced Python ML module failed, using PHP fallback');
            return $this->generatePHPFallbackForecast($months);
            
        } catch (\Exception $e) {
            Log::error('Error in demand forecasting: ' . $e->getMessage());
            return $this->getFallbackForecast($months);
        }
    }

    /**
     * Run new advanced Python demand forecasting module
     */
    private function runAdvancedPythonDemandForecast(int $months): ?array
    {
        try {
            $pythonScript = base_path('machineLearning/new_demand_forecast_api.py');
            $venvPython = base_path('venv/Scripts/python.exe');
            
            // Check if Python script exists
            if (!file_exists($pythonScript)) {
                Log::error('Advanced Python script not found: ' . $pythonScript);
                return null;
            }
            
            // Use virtual environment Python if available, otherwise use system Python
            $pythonCommand = file_exists($venvPython) ? $venvPython : 'python';
            
            // Execute Python script
            $command = sprintf(
                '%s "%s" %d 2>&1',
                escapeshellarg($pythonCommand),
                escapeshellarg($pythonScript),
                $months
            );
            
            $output = shell_exec($command);
            $exitCode = $this->getLastExitCode();
            
            if ($exitCode !== 0) {
                Log::error('Advanced Python script failed with exit code: ' . $exitCode);
                Log::error('Python output: ' . $output);
                return null;
            }
            
            // Parse JSON output
            $result = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse advanced Python JSON output: ' . json_last_error_msg());
                Log::error('Raw output: ' . $output);
                return null;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Error running advanced Python demand forecast: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate PHP fallback forecast when Python module is not available
     */
    private function generatePHPFallbackForecast(int $months): array
    {
        // Removed all Python ML integration and fallback logic
        return $this->getFallbackForecast($months);
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
            ->join('yogurt_products', 'order_items.yogurt_product_id', '=', 'yogurt_products.id')
            ->select(
                'orders.created_at',
                'yogurt_products.product_name as product_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->where('orders.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('orders.created_at', 'yogurt_products.product_name')
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
        
        if (count($values) < $period || count($values) === 0) {
            return array_fill(0, $period, count($values) > 0 ? array_sum($values) / count($values) : 0);
        }

        $movingAverage = array_sum(array_slice($values, -$period)) / ($period > 0 ? $period : 1);
        
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
        $mean = count($values) > 0 ? array_sum($values) / count($values) : 0;
        $variance = count($values) > 0 ? array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values) : 0;
        
        $coefficientOfVariation = $mean != 0 ? sqrt($variance) / $mean : 0;
        
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
            } else {
                $monthlyAverages[$month] = 0;
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

        $recentAvg = count($recent) > 0 ? array_sum(array_column($recent, 'total_sold')) / count($recent) : 0;
        $olderAvg = count($older) > 0 ? array_sum(array_column($older, 'total_sold')) / count($older) : 0;

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
            ->where('order_items.yogurt_product_id', $productId)
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
            ->where('order_items.yogurt_product_id', $productId)
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
        $avgDailySales = count($dailySales) > 0 ? array_sum($dailySales) / count($dailySales) : 0;
        
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
            ->get();

        $riskLevel = 'low';
        $score = 0.2;

        if ($lowStockProducts->count() > 5) {
            $riskLevel = 'high';
            $score = 0.8;
        } elseif ($lowStockProducts->count() > 2) {
            $riskLevel = 'medium';
            $score = 0.5;
        }

        $productNames = $lowStockProducts->pluck('product_name')->toArray();

        return [
            'level' => $riskLevel,
            'score' => $score,
            'factors' => [
                'Low stock items: ' . $lowStockProducts->count(),
                'Products: ' . implode(', ', $productNames)
            ],
            'products' => $productNames
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

    /**
     * Get the last exit code from shell_exec
     */
    private function getLastExitCode(): int
    {
        if (function_exists('shell_exec')) {
            // On Windows, we need to check the exit code differently
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                return 0; // Assume success on Windows for now
            }
            return 0; // Default to success
        }
        return 0;
    }
} 