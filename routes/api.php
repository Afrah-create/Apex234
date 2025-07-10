use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyticsController;

Route::get('/admin-metrics', function () {
    // Example: Replace with your own logic or database queries
    return response()->json([
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        'values' => [12, 19, 3, 5, 2],
    ]);
});

Route::get('/analytics/kpi', [AnalyticsController::class, 'kpi'])->name('api.analytics.kpi');
Route::get('/analytics/predictions', [AnalyticsController::class, 'getPredictions'])->name('api.analytics.predictions');
Route::get('/analytics/trend-analysis', [AnalyticsController::class, 'getTrendAnalysis'])->name('api.analytics.trend-analysis'); 