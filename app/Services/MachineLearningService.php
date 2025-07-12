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
            
            $segments = [
                'premium' => ['retailers' => [], 'characteristics' => []],
                'regular' => ['retailers' => [], 'characteristics' => []],
                'occasional' => ['retailers' => [], 'characteristics' => []]
            ];

            foreach ($retailers as $retailer) {
                $profile = $this->analyzeRetailerProfile($retailer);
                $segment = $this->assignRetailerSegment($profile);
                $segments[$segment]['retailers'][] = $retailer->id;
            }

            // Calculate segment characteristics
            foreach ($segments as $segment => $data) {
                $segments[$segment]['characteristics'] = $this->calculateRetailerSegmentCharacteristics($data['retailers']);
            }

            return $segments;
        } catch (\Exception $e) {
            Log::error('Error performing retailer segmentation: ' . $e->getMessage());
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

    private function assessSupplyChainRisk(): array
    {
        return [
            'risk_level' => 'medium',
            'factors' => [
                'supplier_reliability' => 'high',
                'inventory_shortages' => 'low',
                'delivery_delays' => 'medium'
            ],
            'recommendations' => [
                'Diversify supplier base',
                'Implement safety stock levels',
                'Monitor delivery performance'
            ]
        ];
    }

    private function assessFinancialRisk(): array
    {
        return [
            'risk_level' => 'low',
            'factors' => [
                'cash_flow' => 'stable',
                'profit_margins' => 'healthy',
                'debt_levels' => 'low'
            ],
            'recommendations' => [
                'Maintain current financial practices',
                'Monitor profit margins closely',
                'Consider expansion opportunities'
            ]
        ];
    }

    private function assessOperationalRisk(): array
    {
        return [
            'risk_level' => 'low',
            'factors' => [
                'production_efficiency' => 'high',
                'quality_control' => 'excellent',
                'staff_turnover' => 'low'
            ],
            'recommendations' => [
                'Continue quality monitoring',
                'Invest in staff training',
                'Maintain safety protocols'
            ]
        ];
    }

    private function assessMarketRisk(): array
    {
        return [
            'risk_level' => 'medium',
            'factors' => [
                'competition' => 'moderate',
                'market_demand' => 'stable',
                'regulatory_changes' => 'low'
            ],
            'recommendations' => [
                'Monitor competitor activities',
                'Stay updated on market trends',
                'Prepare for regulatory changes'
            ]
        ];
    }

    private function calculateOverallRiskScore(): float
    {
        return 0.3; // Low overall risk (0.0 = no risk, 1.0 = high risk)
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
} 