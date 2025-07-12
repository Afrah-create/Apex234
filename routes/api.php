<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;

// Machine Learning Analytics Routes
Route::prefix('analytics')->group(function () {
    Route::get('/retailer-segmentation', [AnalyticsController::class, 'getRetailerSegmentation']);
    Route::get('/demand-forecast', [AnalyticsController::class, 'getDemandForecast']);
    Route::get('/sales-predictions', [AnalyticsController::class, 'getPredictions']);
    Route::get('/inventory-optimization', [AnalyticsController::class, 'getInventoryOptimization']);
    Route::get('/risk-assessment', [AnalyticsController::class, 'getRiskAssessment']);
    Route::get('/trend-analysis', [AnalyticsController::class, 'getTrendAnalysis']);
    Route::get('/kpi', [AnalyticsController::class, 'kpi']);
});

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now()->toISOString()
    ]);
}); 