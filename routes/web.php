<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\VendorApplicantController;
use App\Http\Controllers\SupplierController;
use App\Models\YogurtProduct;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Test route for middleware
Route::get('/test-middleware', function () {
    return 'Middleware test successful!';
})->middleware('test');

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user instanceof User) {
        $role = $user->getPrimaryRoleName();
        switch ($role) {
            case 'admin':
                $recentOrders = \App\Models\Order::with(['retailer.user'])
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5);
                $recentOrders->getCollection()->transform(function ($order) {
                    $customerName = $order->retailer && $order->retailer->user ? $order->retailer->user->name : 'N/A';
                    return (object) [
                        'id' => $order->id,
                        'customer_name' => $customerName,
                        'created_at' => $order->created_at,
                        'status' => $order->status,
                        'total' => $order->total,
                    ];
                });

                // User statistics
                $totalUsers = \App\Models\User::count();
                $roles = \App\Models\Role::withCount('users')->get();
                $roleBreakdown = $roles->map(function ($role) use ($totalUsers) {
                    $percentage = $totalUsers > 0 ? round(($role->users_count / $totalUsers) * 100, 1) : 0;
                    return [
                        'role' => $role->name,
                        'count' => $role->users_count,
                        'percentage' => $percentage,
                    ];
                });
                $userStatistics = [
                    'total_users' => $totalUsers,
                    'role_breakdown' => $roleBreakdown,
                ];
                $newVendorApplicants = \App\Models\VendorApplicant::where('status', 'validated')->orderBy('created_at', 'desc')->get();
                return view('dashboard-admin', compact('recentOrders', 'userStatistics', 'newVendorApplicants'));
            case 'retailer':
                return redirect()->route('dashboard.retailer');
            case 'supplier':
                return redirect()->route('dashboard.supplier');
            case 'vendor':
                return redirect()->route('dashboard.vendor');
            default:
                return view('dashboard-admin');
        }
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

// Role-specific dashboards
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/retailer', function () {
        $products = YogurtProduct::all();
        return view('dashboard-retailer', compact('products'));
    })->name('dashboard.retailer');
    Route::get('/dashboard/supplier', function () {
        $totalSupplied = \App\Models\OrderItem::sum('quantity');
        $pendingDeliveries = \App\Models\Delivery::where('delivery_status', 'scheduled')->count();
        $deliveredBatches = \App\Models\Delivery::where('delivery_status', 'delivered')->count();
        return view('dashboard-supplier', compact('totalSupplied', 'pendingDeliveries', 'deliveredBatches'));
    })->name('dashboard.supplier');
    Route::get('/dashboard/vendor', function () {
        return view('dashboard-vendor');
    })->name('dashboard.vendor');
    Route::get('/vendor/manage-orders', function () {
        return view('vendor.manage-orders');
    })->name('vendor.manage-orders');
    Route::get('/vendor/manage-products', function () {
        return view('vendor.manage-products');
    })->name('vendor.manage-products');
    Route::get('/dashboard/employee', function () {
        return view('employee.dashboard');
    })->name('dashboard.employee');
    Route::post('/retailer/orders', [\App\Http\Controllers\RetailerOrderController::class, 'store'])->name('retailer.orders.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin user management
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('index');
    Route::get('/create', [AdminUserController::class, 'create'])->name('create');
    Route::post('/', [AdminUserController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AdminUserController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [AdminUserController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
});

// Admin employee store route
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/employees')->name('admin.employees.')->group(function () {
    Route::post('/', [\App\Http\Controllers\AdminEmployeeController::class, 'store'])->name('store');
});

// Admin order management
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/orders')->name('admin.orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AdminOrderController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AdminOrderController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
    
    // API routes for data
    Route::get('/api/orders-data', [AdminOrderController::class, 'getOrdersData'])->name('api.orders-data');
    Route::get('/api/order-statistics', [AdminOrderController::class, 'getOrderStatistics'])->name('api.order-statistics');
});

// Admin inventory analytics
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/inventory')->name('admin.inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
});

// Inventory API routes for charts
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('api/inventory')->name('api.inventory.')->group(function () {
    Route::get('/chart-data', [InventoryController::class, 'getInventoryChartData'])->name('chart-data');
    Route::get('/summary', [InventoryController::class, 'getInventorySummary'])->name('summary');
    Route::get('/user-statistics', [InventoryController::class, 'getUserStatistics'])->name('user-statistics');
});

// Vendor dashboard API routes
Route::middleware(['auth', 'verified'])->prefix('api/vendor')->name('api.vendor.')->group(function () {
    Route::get('/inventory-summary', [\App\Http\Controllers\VendorDashboardController::class, 'inventorySummary']);
    Route::get('/inventory-chart', [\App\Http\Controllers\VendorDashboardController::class, 'inventoryChart']);
    Route::get('/order-status', [\App\Http\Controllers\VendorDashboardController::class, 'orderStatus']);
    Route::get('/raw-material-stats', [\App\Http\Controllers\VendorDashboardController::class, 'rawMaterialStats']);
    Route::get('/production-summary', [\App\Http\Controllers\VendorDashboardController::class, 'productionSummary']);
});

// Vendor order management API routes
Route::middleware(['auth', 'verified'])->prefix('api/vendor')->group(function () {
    Route::get('/suppliers', [\App\Http\Controllers\VendorOrderController::class, 'suppliers']);
    Route::get('/available-raw-materials', [\App\Http\Controllers\VendorOrderController::class, 'availableRawMaterials']);
    Route::post('/raw-material-orders', [\App\Http\Controllers\VendorOrderController::class, 'placeRawMaterialOrder']);
    Route::get('/raw-material-orders', [\App\Http\Controllers\VendorOrderController::class, 'listRawMaterialOrders']);
    Route::post('/raw-material-orders/{id}/cancel', [\App\Http\Controllers\VendorOrderController::class, 'cancelRawMaterialOrder']);
    Route::get('/product-orders', [\App\Http\Controllers\VendorOrderController::class, 'listProductOrders']);
    Route::post('/product-orders/{id}/confirm', [\App\Http\Controllers\VendorOrderController::class, 'confirmProductOrder']);
});

// Vendor product management API routes
Route::middleware(['auth', 'verified'])->prefix('api/vendor')->group(function () {
    Route::get('/products', [\App\Http\Controllers\VendorProductController::class, 'index']);
    Route::post('/products', [\App\Http\Controllers\VendorProductController::class, 'store']);
    Route::post('/products/{id}', [\App\Http\Controllers\VendorProductController::class, 'update']);
    Route::delete('/products/{id}', [\App\Http\Controllers\VendorProductController::class, 'destroy']);
    Route::post('/products/{id}/toggle-status', [\App\Http\Controllers\VendorProductController::class, 'toggleStatus']);
});

// Vendor application form routes
Route::get('/vendor/apply', [VendorApplicantController::class, 'create'])->name('vendor-applicant.create');
Route::post('/vendor/apply', [VendorApplicantController::class, 'store'])->name('vendor-applicant.store');
Route::get('/vendor/status', [VendorApplicantController::class, 'status'])->name('vendor-applicant.status');
Route::get('/vendor/confirmation', [VendorApplicantController::class, 'confirmation'])->name('vendor-applicant.confirmation');

// Vendor inventory management API routes
Route::middleware(['auth', 'verified'])->prefix('api/vendor/inventory')->name('api.vendor.inventory.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VendorInventoryController::class, 'index'])->name('index');
    Route::post('/products', [\App\Http\Controllers\VendorInventoryController::class, 'storeProductInventory'])->name('store-product');
    Route::put('/products/{id}', [\App\Http\Controllers\VendorInventoryController::class, 'updateProductInventory'])->name('update-product');
    Route::delete('/products/{id}', [\App\Http\Controllers\VendorInventoryController::class, 'deleteProductInventory'])->name('delete-product');
    Route::post('/raw-materials', [\App\Http\Controllers\VendorInventoryController::class, 'storeRawMaterial'])->name('store-raw-material');
    Route::put('/raw-materials/{id}', [\App\Http\Controllers\VendorInventoryController::class, 'updateRawMaterial'])->name('update-raw-material');
    Route::delete('/raw-materials/{id}', [\App\Http\Controllers\VendorInventoryController::class, 'deleteRawMaterial'])->name('delete-raw-material');
    Route::get('/summary', [\App\Http\Controllers\VendorInventoryController::class, 'getInventorySummary'])->name('summary');
    Route::get('/chart-data', [\App\Http\Controllers\VendorInventoryController::class, 'getInventoryChartData'])->name('chart-data');
    Route::get('/dairy-farms', [\App\Http\Controllers\VendorInventoryController::class, 'getDairyFarms'])->name('dairy-farms');
});

// Vendor edit views routes
Route::middleware(['auth', 'verified'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/manage-products', function () {
        return view('vendor.manage-products');
    })->name('manage-products');
    Route::get('/products/{id}/edit', [\App\Http\Controllers\VendorProductController::class, 'show'])->name('products.edit');
    Route::get('/raw-materials/{id}/edit', [\App\Http\Controllers\VendorInventoryController::class, 'showRawMaterial'])->name('raw-materials.edit');
});

// Password reset by token (code) form
Route::get('/password/token', [\App\Http\Controllers\PasswordResetByTokenController::class, 'showForm'])->name('password.token.form');
Route::post('/password/token', [\App\Http\Controllers\PasswordResetByTokenController::class, 'reset'])->name('password.token.reset');

// Temporary test route for analytics (remove in production)
Route::get('/test-analytics', [AnalyticsController::class, 'index'])->name('test.analytics');
Route::get('/test-analytics-controller', [AnalyticsController::class, 'test'])->name('test.analytics.controller');

// Admin analytics and reports
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/analytics')->name('admin.analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
});

// Admin reports route group
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/reports')->name('admin.reports.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminReportController::class, 'index'])->name('index');
});

// Analytics API routes
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('api/analytics')->name('api.analytics.')->group(function () {
    Route::get('/kpi', [AnalyticsController::class, 'getKpiData'])->name('kpi');
    Route::get('/predictions', [AnalyticsController::class, 'getPredictions'])->name('predictions');
    Route::get('/demand-forecast', [AnalyticsController::class, 'getDemandForecast'])->name('demand-forecast');
    Route::get('/customer-segmentation', [AnalyticsController::class, 'getCustomerSegmentation'])->name('customer-segmentation');
    Route::get('/inventory-optimization', [AnalyticsController::class, 'getInventoryOptimization'])->name('inventory-optimization');
    Route::get('/trend-analysis', [AnalyticsController::class, 'getTrendAnalysis'])->name('trend-analysis');
    Route::get('/performance-metrics', [AnalyticsController::class, 'getPerformanceMetrics'])->name('performance-metrics');
    Route::get('/risk-assessment', [AnalyticsController::class, 'getRiskAssessment'])->name('risk-assessment');
    Route::post('/scenario-analysis', [AnalyticsController::class, 'runScenarioAnalysis'])->name('scenario-analysis');
    Route::post('/what-if-analysis', [AnalyticsController::class, 'runWhatIfAnalysis'])->name('what-if-analysis');
    Route::post('/export-report', [AnalyticsController::class, 'exportReport'])->name('export-report');
});

Route::middleware(['auth', 'verified'])->get('/supplier/raw-material-inventory', [SupplierController::class, 'rawMaterialInventory'])->name('supplier.raw-material-inventory');

require __DIR__.'/auth.php';
