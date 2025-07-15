<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\YogurtProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MachineLearningService
{
    /**
     * Generate demand forecasting predictions
     */
    public function generateDemandForecast(int $months = 3): array
    {
        try {
            Log::info('ML: Starting demand forecast generation', ['months' => $months]);
            
            // Get historical sales data
            $historicalData = $this->getHistoricalSalesData();
            
            // Generate forecast using simple statistical methods
            $forecast = $this->generateStatisticalForecast($historicalData, $months);
            
            // Calculate confidence level
            $confidenceLevel = $this->calculateConfidenceLevel($historicalData);
            
            // Detect seasonal patterns
            $seasonalPatterns = $this->detectSeasonalPatterns($historicalData);
            
            // Analyze trend
            $trendDirection = $this->analyzeTrend($historicalData);
            
            return [
                'status' => 'success',
                'forecast' => $forecast,
                'confidence_level' => $confidenceLevel,
                'seasonal_patterns' => $seasonalPatterns,
                'trend_direction' => $trendDirection,
                'generated_at' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('ML: Error in demand forecasting: ' . $e->getMessage());
            return $this->getFallbackForecast($months);
        }
    }

    /**
     * Perform retailer segmentation analysis
     */
    public function performRetailerSegmentation(): array
    {
        try {
            Log::info('ML: Starting retailer segmentation analysis');
            
            $retailers = \App\Models\Retailer::with('orders')->get();
            Log::info('ML: Retrieved retailers', ['count' => $retailers->count()]);
            
            $segments = [
                'premium' => ['retailers' => [], 'characteristics' => []],
                'regular' => ['retailers' => [], 'characteristics' => []],
                'occasional' => ['retailers' => [], 'characteristics' => []]
            ];

            foreach ($retailers as $retailer) {
                $profile = $this->analyzeRetailerProfile($retailer);
                $segment = $this->assignRetailerSegment($profile);
                $segments[$segment]['retailers'][] = $retailer->id;
                
                Log::info('ML: Retailer segmented', [
                    'retailer_id' => $retailer->id,
                    'store_name' => $retailer->store_name,
                    'segment' => $segment,
                    'profile' => $profile
                ]);
            }

            // Calculate segment characteristics
            foreach ($segments as $segment => $data) {
                $segments[$segment]['characteristics'] = $this->calculateRetailerSegmentCharacteristics($data['retailers']);
                Log::info('ML: Segment calculated', [
                    'segment' => $segment,
                    'retailer_count' => count($data['retailers']),
                    'characteristics' => $segments[$segment]['characteristics']
                ]);
            }

            Log::info('ML: Retailer segmentation completed', ['segments' => $segments]);
            return $segments;
        } catch (\Exception $e) {
            Log::error('Error performing retailer segmentation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackSegmentation();
        }
    }

    /**
     * Perform customer segmentation analysis
     */
    public function performCustomerSegmentation(): array
    {
        try {
            Log::info('ML: Starting customer segmentation analysis');
            
            $customers = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'retailer'); // Change to 'customer' if you have a separate customer role
            })->with('orders')->get();
            Log::info('ML: Retrieved customers', ['count' => $customers->count()]);
            
            $segments = [
                'premium' => ['customers' => [], 'characteristics' => []],
                'regular' => ['customers' => [], 'characteristics' => []],
                'occasional' => ['customers' => [], 'characteristics' => []]
            ];

            foreach ($customers as $customer) {
                $profile = $this->analyzeCustomerProfile($customer);
                $segment = $this->assignCustomerSegment($profile);
                $segments[$segment]['customers'][] = $customer->id;
                
                Log::info('ML: Customer segmented', [
                    'customer_id' => $customer->id,
                    'name' => $customer->name,
                    'segment' => $segment,
                    'profile' => $profile
                ]);
            }

            // Calculate segment characteristics
            foreach ($segments as $segment => $data) {
                $segments[$segment]['characteristics'] = $this->calculateCustomerSegmentCharacteristics($data['customers']);
                Log::info('ML: Segment calculated', [
                    'segment' => $segment,
                    'customer_count' => count($data['customers']),
                    'characteristics' => $segments[$segment]['characteristics']
                ]);
            }

            Log::info('Customer Segmentation Final Structure', $segments);
            return $segments;
        } catch (\Exception $e) {
            Log::error('Error performing customer segmentation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackSegmentation();
        }
    }

    /**
     * Generate inventory optimization recommendations
     */
    public function generateInventoryRecommendations(): array
    {
        try {
            Log::info('ML: Starting inventory optimization analysis');
            
            $products = YogurtProduct::with('inventory')->get();
            $recommendations = [];

            foreach ($products as $product) {
                $analysis = $this->analyzeProductInventory($product);
                $recommendations[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
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
            Log::info('ML: Starting sales prediction', ['days' => $days]);
            
            $predictions = [];

            // Predict for each product
            $products = YogurtProduct::all();
            foreach ($products as $product) {
                $productSales = $this->getProductSalesHistory($product->id);
                $prediction = $this->predictProductSales($productSales, $days);
                
                $predictions[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
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
            Log::info('ML: Starting business risk assessment');
            
            return [
                'supply_chain_risks' => $this->assessSupplyChainRisk(),
                'financial_risks' => $this->assessFinancialRisk(),
                'operational_risks' => $this->assessOperationalRisk(),
                'market_risks' => $this->assessMarketRisk(),
                'overall_risk_score' => $this->calculateOverallRiskScore()
            ];
        } catch (\Exception $e) {
            Log::error('Error assessing business risks: ' . $e->getMessage());
            return $this->getFallbackRiskAssessment();
        }
    }

    // Private helper methods

    private function getHistoricalSalesData(): array
    {
        // Get last 12 months of sales data
        $startDate = Carbon::now()->subMonths(12);
        
        $salesData = Order::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            SUM(total_amount) as total_sales,
            COUNT(*) as order_count
        ')
        ->where('created_at', '>=', $startDate)
        ->groupBy('month')
        ->orderBy('month')
            ->get()
            ->toArray();

        // If no real data, generate sample data
        if (empty($salesData)) {
            return $this->generateSampleSalesData();
        }

        return $salesData;
    }

    private function generateStatisticalForecast(array $historicalData, int $months): array
    {
        $forecast = [];
        
        // Calculate average monthly sales
        $totalSales = array_sum(array_column($historicalData, 'total_sales'));
        $avgMonthlySales = count($historicalData) > 0 ? $totalSales / count($historicalData) : 50000;
        
        // Calculate trend (simple linear regression)
        $trend = $this->calculateTrend($historicalData);
        
        // Generate forecast for each month
        for ($i = 1; $i <= $months; $i++) {
            $forecastValue = $avgMonthlySales + ($trend * $i);
            $forecast[] = [
                'month' => Carbon::now()->addMonths($i)->format('Y-m'),
                'predicted_sales' => round($forecastValue, 2),
                'confidence_interval' => round($forecastValue * 0.15, 2) // 15% margin
            ];
        }

        return $forecast;
    }

    private function calculateTrend(array $data): float
    {
        if (count($data) < 2) return 0;
        
        $n = count($data);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($data as $index => $point) {
            $x = $index + 1;
            $y = $point['total_sales'];
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        return $slope;
    }

    private function calculateConfidenceLevel(array $data): float
    {
        if (count($data) < 2) return 0.5;
        
        // Calculate coefficient of variation
        $values = array_column($data, 'total_sales');
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        $stdDev = sqrt($variance);
        $cv = $stdDev / $mean;
        
        // Convert to confidence level (lower CV = higher confidence)
        return max(0.1, min(0.95, 1 - $cv));
    }

    private function detectSeasonalPatterns(array $data): array
    {
        // Simple seasonal pattern detection
        $monthlyAverages = [];
        
        foreach ($data as $point) {
            $month = Carbon::createFromFormat('Y-m', $point['month'])->month;
            if (!isset($monthlyAverages[$month])) {
                $monthlyAverages[$month] = [];
            }
            $monthlyAverages[$month][] = $point['total_sales'];
        }
        
        $patterns = [];
        foreach ($monthlyAverages as $month => $values) {
            $patterns[$month] = [
                'average_sales' => array_sum($values) / count($values),
                'trend' => count($values) > 1 ? 'increasing' : 'stable'
            ];
        }
        
        return $patterns;
    }

    private function analyzeTrend(array $data): string
    {
        $trend = $this->calculateTrend($data);
        
        if ($trend > 1000) return 'strong_increasing';
        if ($trend > 100) return 'increasing';
        if ($trend < -1000) return 'strong_decreasing';
        if ($trend < -100) return 'decreasing';
        return 'stable';
    }

    // Analyze retailer profile for segmentation
    private function analyzeRetailerProfile(\App\Models\Retailer $retailer): array
    {
        $orders = $retailer->orders;
        return [
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('total_amount'),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            'last_order_date' => $orders->max('created_at'),
            'order_frequency' => $this->calculateOrderFrequency($orders)
        ];
    }

    // Assign retailer to a segment
    private function assignRetailerSegment(array $profile): string
    {
        $totalSpent = $profile['total_spent'];
        $orderFrequency = $profile['order_frequency'];
        if ($totalSpent > 1000 && $orderFrequency > 2) {
            return 'premium';
        } elseif ($totalSpent > 500 || $orderFrequency > 1) {
            return 'regular';
        } else {
            return 'occasional';
        }
    }

    // Calculate segment characteristics for retailers
    private function calculateRetailerSegmentCharacteristics(array $retailerIds): array
    {
        if (empty($retailerIds)) {
            return [
                'avg_order_value' => 0,
                'retention_rate' => 0,
                'lifetime_value' => 0
            ];
        }
        $retailers = \App\Models\Retailer::whereIn('id', $retailerIds)->with('orders')->get();
        $totalOrders = $retailers->sum(function($retailer) {
            return $retailer->orders->count();
        });
        $totalSpent = $retailers->sum(function($retailer) {
            return $retailer->orders->sum('total_amount');
        });
        return [
            'avg_order_value' => $totalOrders > 0 ? $totalSpent / $totalOrders : 0,
            'retention_rate' => $this->calculateRetailerRetentionRate($retailers),
            'lifetime_value' => $totalSpent / count($retailerIds)
        ];
    }

    // Calculate retention rate for retailers
    private function calculateRetailerRetentionRate($retailers): float
    {
        if ($retailers->count() < 2) return 0;
        $returning = $retailers->filter(function($retailer) {
            return $retailer->orders->count() > 1;
        })->count();
        return $returning / $retailers->count();
    }

    private function calculateOrderFrequency($orders): float
    {
        if ($orders->count() < 2) return 0;
        
        $firstOrder = $orders->min('created_at');
        $lastOrder = $orders->max('created_at');
        $daysDiff = Carbon::parse($firstOrder)->diffInDays(Carbon::parse($lastOrder));
        
        return $daysDiff > 0 ? $orders->count() / ($daysDiff / 30) : 0; // orders per month
    }

    private function analyzeProductInventory(YogurtProduct $product): array
    {
        $currentStock = $product->stock ?? 0;
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
        }

        return [
            'current_stock' => $currentStock,
            'optimal_stock' => round($optimalStock, 2),
            'reorder_point' => round($reorderPoint, 2),
            'recommendation' => $recommendation,
            'urgency' => $urgency
        ];
    }

    private function calculateAverageMonthlyDemand(int $productId): float
    {
        $startDate = Carbon::now()->subMonths(6);
        
        $totalDemand = OrderItem::where('yogurt_product_id', $productId)
            ->whereHas('order', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->sum('quantity');
        
        return $totalDemand / 6; // Average per month
    }

    private function getProductSalesHistory(int $productId): array
    {
        $startDate = Carbon::now()->subMonths(6);
        
        return OrderItem::where('yogurt_product_id', $productId)
            ->whereHas('order', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m") as month, SUM(quantity) as total_sales')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function predictProductSales(array $salesHistory, int $days): array
    {
        if (empty($salesHistory)) {
            return [
                'sales' => 100, // Default prediction
                'confidence' => 0.5,
                'trend' => 'stable'
            ];
        }

        $totalSales = array_sum(array_column($salesHistory, 'total_sales'));
        $avgMonthlySales = $totalSales / count($salesHistory);
        $dailySales = $avgMonthlySales / 30;
        $predictedSales = $dailySales * $days;

        return [
            'sales' => round($predictedSales, 2),
            'confidence' => 0.7,
            'trend' => 'stable'
        ];
    }

    private function analyzeCustomerProfile(\App\Models\User $customer): array
    {
        $orders = $customer->orders;
        $orderTypeCounts = $orders->groupBy('order_type')->map->count()->toArray();
        return [
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('total_amount'),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            'last_order_date' => $orders->max('created_at'),
            'order_frequency' => $this->calculateOrderFrequency($orders),
            'order_type_counts' => $orderTypeCounts, // NEW
        ];
    }

    private function assignCustomerSegment(array $profile): string
    {
        $totalSpent = $profile['total_spent'];
        $orderFrequency = $profile['order_frequency'];
        $orderTypeCounts = $profile['order_type_counts'] ?? [];
        $rushOrders = $orderTypeCounts['rush'] ?? 0;
        $bulkOrders = $orderTypeCounts['bulk'] ?? 0;
        $regularOrders = $orderTypeCounts['regular'] ?? 0;

        if (($rushOrders + $bulkOrders) > 2 || ($totalSpent > 1000 && $orderFrequency > 2)) {
            return 'premium';
        } elseif ($regularOrders > 1 || $totalSpent > 500 || $orderFrequency > 1) {
            return 'regular';
        } else {
            return 'occasional';
        }
    }

    private function calculateCustomerSegmentCharacteristics(array $customerIds): array
    {
        if (empty($customerIds)) {
            return [
                'avg_order_value' => 0,
                'retention_rate' => 0,
                'lifetime_value' => 0
            ];
        }
        $customers = \App\Models\User::whereIn('id', $customerIds)->with('orders')->get();
        $totalOrders = $customers->sum(function($customer) {
            return $customer->orders->count();
        });
        $totalSpent = $customers->sum(function($customer) {
            return $customer->orders->sum('total_amount');
        });
        return [
            'avg_order_value' => $totalOrders > 0 ? $totalSpent / $totalOrders : 0,
            'retention_rate' => $this->calculateCustomerRetentionRate($customers),
            'lifetime_value' => $totalSpent / count($customerIds)
        ];
    }

    private function calculateCustomerRetentionRate($customers): float
    {
        if ($customers->count() < 2) return 0;
        $returning = $customers->filter(function($customer) {
            return $customer->orders->count() > 1;
        })->count();
        return $returning / $customers->count();
    }

    private function assessSupplyChainRisk(): array
    {
        try {
            // Analyze supplier performance
            $suppliers = \App\Models\Supplier::with(['user'])->get();
            $supplierCount = $suppliers->count();
            $activeSuppliers = $suppliers->where('status', 'approved')->count();
            $supplierReliability = $supplierCount > 0 ? ($activeSuppliers / $supplierCount) * 100 : 0;
            
            // Analyze inventory shortages
            $products = YogurtProduct::all();
            $lowStockProducts = 0;
            $outOfStockProducts = 0;
            
            foreach ($products as $product) {
                if (($product->stock ?? 0) <= 0) {
                    $outOfStockProducts++;
                } elseif (($product->stock ?? 0) < 10) {
                    $lowStockProducts++;
                }
            }
            
            $shortageRate = $products->count() > 0 ? (($lowStockProducts + $outOfStockProducts) / $products->count()) * 100 : 0;
            
            // Analyze delivery performance (using raw material orders)
            $recentOrders = \App\Models\RawMaterialOrder::where('created_at', '>=', Carbon::now()->subMonths(3))->get();
            $deliveredOrders = $recentOrders->where('status', 'delivered')->count();
            $delayedOrders = $recentOrders->whereIn('status', ['pending', 'processing'])->count();
            $deliveryPerformance = $recentOrders->count() > 0 ? ($deliveredOrders / $recentOrders->count()) * 100 : 100;
            
            // Calculate risk level
            $riskScore = 0;
            if ($supplierReliability < 70) $riskScore += 30;
            if ($shortageRate > 20) $riskScore += 40;
            if ($deliveryPerformance < 80) $riskScore += 30;
            
            $riskLevel = $riskScore >= 60 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');
            
            return [
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'factors' => [
                    'supplier_reliability' => $supplierReliability >= 80 ? 'high' : ($supplierReliability >= 60 ? 'medium' : 'low'),
                    'inventory_shortages' => $shortageRate <= 10 ? 'low' : ($shortageRate <= 20 ? 'medium' : 'high'),
                    'delivery_delays' => $deliveryPerformance >= 90 ? 'low' : ($deliveryPerformance >= 75 ? 'medium' : 'high')
                ],
                'metrics' => [
                    'active_suppliers' => $activeSuppliers,
                    'total_suppliers' => $supplierCount,
                    'supplier_reliability_percentage' => round($supplierReliability, 1),
                    'shortage_rate_percentage' => round($shortageRate, 1),
                    'delivery_performance_percentage' => round($deliveryPerformance, 1),
                    'low_stock_products' => $lowStockProducts,
                    'out_of_stock_products' => $outOfStockProducts
                ],
                'recommendations' => $this->generateSupplyChainRecommendations($riskLevel, $supplierReliability, $shortageRate, $deliveryPerformance)
            ];
        } catch (\Exception $e) {
            Log::error('Error assessing supply chain risk: ' . $e->getMessage());
            return $this->getFallbackSupplyChainRisk();
        }
    }

    private function assessFinancialRisk(): array
    {
        try {
            // Analyze revenue trends
            $currentMonthRevenue = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');
            
            $lastMonthRevenue = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('total_amount');
            
            $revenueGrowth = $lastMonthRevenue > 0 ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
            
            // Analyze profit margins (simplified calculation)
            $totalRevenue = Order::where('created_at', '>=', Carbon::now()->subMonths(3))->sum('total_amount');
            $totalCost = Inventory::where('created_at', '>=', Carbon::now()->subMonths(3))->sum('total_value');
            $profitMargin = $totalRevenue > 0 ? (($totalRevenue - $totalCost) / $totalRevenue) * 100 : 0;
            
            // Analyze cash flow (using order payment status)
            $paidOrders = Order::where('payment_status', 'paid')->count();
            $totalOrders = Order::count();
            $paymentRate = $totalOrders > 0 ? ($paidOrders / $totalOrders) * 100 : 0;
            
            // Calculate risk level
            $riskScore = 0;
            if ($revenueGrowth < -10) $riskScore += 40;
            if ($profitMargin < 15) $riskScore += 30;
            if ($paymentRate < 80) $riskScore += 30;
            
            $riskLevel = $riskScore >= 60 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');
            
            return [
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'factors' => [
                    'cash_flow' => $paymentRate >= 90 ? 'stable' : ($paymentRate >= 75 ? 'moderate' : 'unstable'),
                    'profit_margins' => $profitMargin >= 20 ? 'healthy' : ($profitMargin >= 10 ? 'moderate' : 'low'),
                    'revenue_growth' => $revenueGrowth >= 5 ? 'positive' : ($revenueGrowth >= -5 ? 'stable' : 'declining')
                ],
                'metrics' => [
                    'current_month_revenue' => round($currentMonthRevenue, 2),
                    'last_month_revenue' => round($lastMonthRevenue, 2),
                    'revenue_growth_percentage' => round($revenueGrowth, 1),
                    'profit_margin_percentage' => round($profitMargin, 1),
                    'payment_rate_percentage' => round($paymentRate, 1),
                    'total_revenue_3months' => round($totalRevenue, 2),
                    'total_cost_3months' => round($totalCost, 2)
                ],
                'recommendations' => $this->generateFinancialRecommendations($riskLevel, $revenueGrowth, $profitMargin, $paymentRate)
            ];
        } catch (\Exception $e) {
            Log::error('Error assessing financial risk: ' . $e->getMessage());
            return $this->getFallbackFinancialRisk();
        }
    }

    private function assessOperationalRisk(): array
    {
        try {
            // Analyze production efficiency (using order fulfillment)
            $totalOrders = Order::count();
            $completedOrders = Order::where('order_status', 'delivered')->count();
            $fulfillmentRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 100;
            
            // Analyze quality control (using inventory status)
            $totalInventory = Inventory::count();
            $damagedInventory = Inventory::where('quantity_damaged', '>', 0)->count();
            $qualityRate = $totalInventory > 0 ? (($totalInventory - $damagedInventory) / $totalInventory) * 100 : 100;
            
            // Analyze staff efficiency (using order processing time)
            $recentOrders = Order::where('created_at', '>=', Carbon::now()->subMonths(1))->get();
            $avgProcessingTime = 0;
            if ($recentOrders->count() > 0) {
                $totalTime = 0;
                foreach ($recentOrders as $order) {
                    if ($order->order_status === 'delivered' && $order->created_at && $order->updated_at) {
                        $totalTime += Carbon::parse($order->created_at)->diffInHours($order->updated_at);
                    }
                }
                $avgProcessingTime = $totalTime / $recentOrders->count();
            }
            
            // Calculate risk level
            $riskScore = 0;
            if ($fulfillmentRate < 85) $riskScore += 35;
            if ($qualityRate < 90) $riskScore += 35;
            if ($avgProcessingTime > 72) $riskScore += 30; // More than 3 days
            
            $riskLevel = $riskScore >= 60 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');
            
            return [
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'factors' => [
                    'production_efficiency' => $fulfillmentRate >= 95 ? 'high' : ($fulfillmentRate >= 85 ? 'moderate' : 'low'),
                    'quality_control' => $qualityRate >= 95 ? 'excellent' : ($qualityRate >= 85 ? 'good' : 'needs_improvement'),
                    'staff_turnover' => $avgProcessingTime <= 48 ? 'low' : ($avgProcessingTime <= 72 ? 'moderate' : 'high')
                ],
                'metrics' => [
                    'fulfillment_rate_percentage' => round($fulfillmentRate, 1),
                    'quality_rate_percentage' => round($qualityRate, 1),
                    'avg_processing_time_hours' => round($avgProcessingTime, 1),
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'damaged_inventory_count' => $damagedInventory
                ],
                'recommendations' => $this->generateOperationalRecommendations($riskLevel, $fulfillmentRate, $qualityRate, $avgProcessingTime)
            ];
        } catch (\Exception $e) {
            Log::error('Error assessing operational risk: ' . $e->getMessage());
            return $this->getFallbackOperationalRisk();
        }
    }

    private function assessMarketRisk(): array
    {
        try {
            // Analyze market demand (using order trends)
            $currentMonthOrders = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();
            
            $lastMonthOrders = Order::whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->count();
            
            $demandGrowth = $lastMonthOrders > 0 ? (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 : 0;
            
            // Analyze customer retention
            $totalCustomers = User::whereHas('roles', function($query) {
                $query->where('name', 'retailer');
            })->count();
            
            $activeCustomers = User::whereHas('roles', function($query) {
                $query->where('name', 'retailer');
            })->whereHas('orders', function($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths(3));
            })->count();
            
            $retentionRate = $totalCustomers > 0 ? ($activeCustomers / $totalCustomers) * 100 : 0;
            
            // Analyze competition (using price sensitivity)
            $avgOrderValue = Order::avg('total_amount') ?? 0;
            $priceSensitivity = $avgOrderValue < 100 ? 'high' : ($avgOrderValue < 200 ? 'moderate' : 'low');
            
            // Calculate risk level
            $riskScore = 0;
            if ($demandGrowth < -15) $riskScore += 40;
            if ($retentionRate < 70) $riskScore += 30;
            if ($priceSensitivity === 'high') $riskScore += 30;
            
            $riskLevel = $riskScore >= 60 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');
            
            return [
                'risk_level' => $riskLevel,
                'risk_score' => $riskScore,
                'factors' => [
                    'competition' => $priceSensitivity === 'high' ? 'high' : ($priceSensitivity === 'moderate' ? 'moderate' : 'low'),
                    'market_demand' => $demandGrowth >= 5 ? 'growing' : ($demandGrowth >= -5 ? 'stable' : 'declining'),
                    'customer_retention' => $retentionRate >= 80 ? 'high' : ($retentionRate >= 60 ? 'moderate' : 'low')
                ],
                'metrics' => [
                    'current_month_orders' => $currentMonthOrders,
                    'last_month_orders' => $lastMonthOrders,
                    'demand_growth_percentage' => round($demandGrowth, 1),
                    'retention_rate_percentage' => round($retentionRate, 1),
                    'avg_order_value' => round($avgOrderValue, 2),
                    'total_customers' => $totalCustomers,
                    'active_customers' => $activeCustomers
                ],
                'recommendations' => $this->generateMarketRecommendations($riskLevel, $demandGrowth, $retentionRate, $priceSensitivity)
            ];
        } catch (\Exception $e) {
            Log::error('Error assessing market risk: ' . $e->getMessage());
            return $this->getFallbackMarketRisk();
        }
    }

    private function calculateOverallRiskScore(): float
    {
        try {
            $supplyChainRisk = $this->assessSupplyChainRisk();
            $financialRisk = $this->assessFinancialRisk();
            $operationalRisk = $this->assessOperationalRisk();
            $marketRisk = $this->assessMarketRisk();
            
            $riskScores = [
                $supplyChainRisk['risk_score'] ?? 0,
                $financialRisk['risk_score'] ?? 0,
                $operationalRisk['risk_score'] ?? 0,
                $marketRisk['risk_score'] ?? 0
            ];
            
            $overallScore = array_sum($riskScores) / count($riskScores);
            return round($overallScore / 100, 2); // Normalize to 0-1 scale
        } catch (\Exception $e) {
            Log::error('Error calculating overall risk score: ' . $e->getMessage());
            return 0.3; // Fallback score
        }
    }

    private function generateSampleSalesData(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $data[] = [
                'month' => $month->format('Y-m'),
                'total_sales' => rand(40000, 60000),
                'order_count' => rand(50, 100)
            ];
        }
        return $data;
    }

    // Fallback methods for error handling
    private function getFallbackForecast(int $months): array
    {
        return [
            'status' => 'fallback',
            'forecast' => array_map(function($i) {
                return [
                    'month' => Carbon::now()->addMonths($i)->format('Y-m'),
                    'predicted_sales' => 50000,
                    'confidence_interval' => 7500
                ];
            }, range(1, $months)),
            'confidence_level' => 0.5,
            'seasonal_patterns' => [],
            'trend_direction' => 'stable',
            'generated_at' => now()->toISOString()
        ];
    }

    private function getFallbackSegmentation(): array
    {
        return [
            'premium' => ['retailers' => [], 'characteristics' => ['avg_order_value' => 0, 'retention_rate' => 0, 'lifetime_value' => 0]],
            'regular' => ['retailers' => [], 'characteristics' => ['avg_order_value' => 0, 'retention_rate' => 0, 'lifetime_value' => 0]],
            'occasional' => ['retailers' => [], 'characteristics' => ['avg_order_value' => 0, 'retention_rate' => 0, 'lifetime_value' => 0]]
        ];
    }

    private function getFallbackSalesPredictions(int $days): array
    {
        return [
            [
                'product_id' => 1,
                'product_name' => 'Sample Product',
                'predicted_sales' => 100,
                'confidence_interval' => 15,
                'trend' => 'stable'
            ]
        ];
    }

    private function getFallbackRiskAssessment(): array
    {
        return [
            'supply_chain_risks' => ['risk_level' => 'medium'],
            'financial_risks' => ['risk_level' => 'low'],
            'operational_risks' => ['risk_level' => 'low'],
            'market_risks' => ['risk_level' => 'medium'],
            'overall_risk_score' => 0.3
        ];
    }

    private function generateSupplyChainRecommendations(string $riskLevel, float $supplierReliability, float $shortageRate, float $deliveryPerformance): array
    {
        $recommendations = [];
        
        if ($supplierReliability < 70) {
            $recommendations[] = 'Diversify supplier base to reduce reliance on single suppliers';
        }
        if ($shortageRate > 20) {
            $recommendations[] = 'Implement safety stock levels to mitigate inventory shortages';
        }
        if ($deliveryPerformance < 80) {
            $recommendations[] = 'Monitor delivery performance and identify frequent delays';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Maintain current supplier relationships and monitor performance';
            $recommendations[] = 'Regularly review inventory levels and reorder points';
        }
        
        return $recommendations;
    }

    private function generateFinancialRecommendations(string $riskLevel, float $revenueGrowth, float $profitMargin, float $paymentRate): array
    {
        $recommendations = [];
        
        if ($revenueGrowth < -10) {
            $recommendations[] = 'Focus on revenue growth strategies and market expansion';
        }
        if ($profitMargin < 15) {
            $recommendations[] = 'Implement cost control measures and optimize pricing';
        }
        if ($paymentRate < 80) {
            $recommendations[] = 'Improve cash flow by accelerating payment collection';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Maintain current financial practices and monitor margins';
            $recommendations[] = 'Consider expansion opportunities if growth is positive';
        }
        
        return $recommendations;
    }

    private function generateOperationalRecommendations(string $riskLevel, float $fulfillmentRate, float $qualityRate, float $avgProcessingTime): array
    {
        $recommendations = [];
        
        if ($fulfillmentRate < 85) {
            $recommendations[] = 'Optimize production processes to improve fulfillment rates';
        }
        if ($qualityRate < 90) {
            $recommendations[] = 'Implement robust quality control systems';
        }
        if ($avgProcessingTime > 72) {
            $recommendations[] = 'Streamline order processing and reduce delivery times';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Continue quality monitoring and staff training';
            $recommendations[] = 'Maintain safety protocols and procedures';
        }
        
        return $recommendations;
    }

    private function generateMarketRecommendations(string $riskLevel, float $demandGrowth, float $retentionRate, string $priceSensitivity): array
    {
        $recommendations = [];
        
        if ($demandGrowth < -15) {
            $recommendations[] = 'Analyze market trends and adapt product offerings';
        }
        if ($retentionRate < 70) {
            $recommendations[] = 'Implement customer retention strategies and loyalty programs';
        }
        if ($priceSensitivity === 'high') {
            $recommendations[] = 'Monitor competitor pricing and optimize price strategies';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Monitor competitor activities and market trends';
            $recommendations[] = 'Stay updated on customer preferences';
        }
        
        return $recommendations;
    }

    private function getFallbackSupplyChainRisk(): array
    {
        return [
            'risk_level' => 'medium',
            'risk_score' => 50,
            'factors' => [
                'supplier_reliability' => 'medium',
                'inventory_shortages' => 'medium',
                'delivery_delays' => 'medium'
            ],
            'metrics' => [
                'active_suppliers' => 5,
                'total_suppliers' => 10,
                'supplier_reliability_percentage' => 70,
                'shortage_rate_percentage' => 15,
                'delivery_performance_percentage' => 85,
                'low_stock_products' => 10,
                'out_of_stock_products' => 5
            ],
            'recommendations' => [
                'Diversify supplier base to reduce reliance on single suppliers',
                'Implement safety stock levels to mitigate inventory shortages',
                'Monitor delivery performance and identify frequent delays'
            ]
        ];
    }

    private function getFallbackFinancialRisk(): array
    {
        return [
            'risk_level' => 'medium',
            'risk_score' => 50,
            'factors' => [
                'cash_flow' => 'moderate',
                'profit_margins' => 'moderate',
                'revenue_growth' => 'stable'
            ],
            'metrics' => [
                'current_month_revenue' => 50000,
                'last_month_revenue' => 45000,
                'revenue_growth_percentage' => 11.1,
                'profit_margin_percentage' => 15,
                'payment_rate_percentage' => 85,
                'total_revenue_3months' => 145000,
                'total_cost_3months' => 100000
            ],
            'recommendations' => [
                'Focus on revenue growth strategies and market expansion',
                'Implement cost control measures and optimize pricing',
                'Improve cash flow by accelerating payment collection'
            ]
        ];
    }

    private function getFallbackOperationalRisk(): array
    {
        return [
            'risk_level' => 'medium',
            'risk_score' => 50,
            'factors' => [
                'production_efficiency' => 'moderate',
                'quality_control' => 'good',
                'staff_turnover' => 'moderate'
            ],
            'metrics' => [
                'fulfillment_rate_percentage' => 90,
                'quality_rate_percentage' => 92,
                'avg_processing_time_hours' => 48,
                'total_orders' => 1000,
                'completed_orders' => 900,
                'damaged_inventory_count' => 5
            ],
            'recommendations' => [
                'Optimize production processes to improve fulfillment rates',
                'Implement robust quality control systems',
                'Streamline order processing and reduce delivery times'
            ]
        ];
    }

    private function getFallbackMarketRisk(): array
    {
        return [
            'risk_level' => 'medium',
            'risk_score' => 50,
            'factors' => [
                'competition' => 'moderate',
                'market_demand' => 'stable',
                'customer_retention' => 'moderate'
            ],
            'metrics' => [
                'current_month_orders' => 1000,
                'last_month_orders' => 900,
                'demand_growth_percentage' => 11.1,
                'retention_rate_percentage' => 75,
                'avg_order_value' => 150,
                'total_customers' => 100,
                'active_customers' => 75
            ],
            'recommendations' => [
                'Analyze market trends and adapt product offerings',
                'Implement customer retention strategies and loyalty programs',
                'Monitor competitor pricing and optimize price strategies'
            ]
        ];
    }
} 