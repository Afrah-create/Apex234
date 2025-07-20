<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AnalyticsController;

// Machine Learning Analytics Routes
Route::get('analytics/retailer-segmentation', [AnalyticsController::class, 'getRetailerSegmentation']);
Route::get('analytics/demand-forecast', [AnalyticsController::class, 'getDemandForecast']);
Route::get('analytics/sales-predictions', [AnalyticsController::class, 'getPredictions']);
Route::get('analytics/inventory-optimization', [AnalyticsController::class, 'getInventoryOptimization']);
Route::get('analytics/risk-assessment', [AnalyticsController::class, 'getRiskAssessment']);
Route::get('analytics/trend-analysis', [AnalyticsController::class, 'getTrendAnalysis']);
Route::get('analytics/kpi', [AnalyticsController::class, 'kpi']);
Route::get('analytics/customer-segmentation', [App\Http\Controllers\AnalyticsController::class, 'getCustomerSegmentation']);

// Add this route for fetching all distribution centers
Route::get('/distribution-centers', function() {
    return \App\Models\DistributionCenter::select('id', 'center_name')->get();
})->name('api.distribution-centers.index');

// Test route to verify API is working
Route::get('test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now()->toISOString()
    ]);
});

// Notification API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', function (Request $request) {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->take(20)->get();
        $unreadCount = $user->unreadNotifications()->count();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    });

    Route::post('/notifications/{id}/mark-read', function (Request $request, $id) {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    });

    Route::post('/notifications/mark-all-read', function (Request $request) {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    });
}); 