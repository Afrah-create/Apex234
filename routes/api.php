<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;

// Machine Learning Analytics Routes
Route::get('analytics/retailer-segmentation', [AnalyticsController::class, 'getRetailerSegmentation']);
Route::get('analytics/demand-forecast', [AnalyticsController::class, 'getDemandForecast']);
Route::get('analytics/sales-predictions', [AnalyticsController::class, 'getPredictions']);
Route::get('analytics/inventory-optimization', [AnalyticsController::class, 'getInventoryOptimization']);
Route::get('analytics/risk-assessment', [AnalyticsController::class, 'getRiskAssessment']);
Route::get('analytics/trend-analysis', [AnalyticsController::class, 'getTrendAnalysis']);
Route::get('analytics/kpi', [AnalyticsController::class, 'kpi']);

// Test route to verify API is working
Route::get('test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now()->toISOString()
    ]);
}); 