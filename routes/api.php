use Illuminate\Support\Facades\Route;

Route::get('/admin-metrics', function () {
    // Example: Replace with your own logic or database queries
    return response()->json([
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        'values' => [12, 19, 3, 5, 2],
    ]);
}); 