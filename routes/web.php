<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
                return view('dashboard-admin');
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
        return view('dashboard-retailer');
    })->name('dashboard.retailer');
    Route::get('/dashboard/supplier', function () {
        return view('dashboard-supplier');
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

// Inventory API routes for charts
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('api/inventory')->name('api.inventory.')->group(function () {
    Route::get('/chart-data', [InventoryController::class, 'getInventoryChartData'])->name('chart-data');
    Route::get('/summary', [InventoryController::class, 'getInventorySummary'])->name('summary');
    Route::get('/user-statistics', [InventoryController::class, 'getUserStatistics'])->name('user-statistics');
});

// Temporary test route (remove in production)
Route::get('/test-inventory', function() {
    $inventoryData = \App\Models\Inventory::join('yogurt_products', 'inventories.yogurt_product_id', '=', 'yogurt_products.id')
        ->select(
            'yogurt_products.product_name as product_name',
            \Illuminate\Support\Facades\DB::raw('SUM(inventories.quantity_available) as total_available'),
            \Illuminate\Support\Facades\DB::raw('SUM(inventories.quantity_reserved) as total_reserved'),
            \Illuminate\Support\Facades\DB::raw('SUM(inventories.quantity_damaged) as total_damaged'),
            \Illuminate\Support\Facades\DB::raw('SUM(inventories.quantity_expired) as total_expired')
        )
        ->groupBy('yogurt_products.id', 'yogurt_products.product_name')
        ->get();
    
    return response()->json($inventoryData);
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
    Route::post('/raw-material-orders', [\App\Http\Controllers\VendorOrderController::class, 'placeRawMaterialOrder']);
    Route::get('/raw-material-orders', [\App\Http\Controllers\VendorOrderController::class, 'listRawMaterialOrders']);
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

require __DIR__.'/auth.php';
