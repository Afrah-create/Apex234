<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard-admin');
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin user management
Route::middleware(['auth', 'verified'])->prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('index');
    Route::get('/create', [AdminUserController::class, 'create'])->name('create');
    Route::post('/', [AdminUserController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AdminUserController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [AdminUserController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
});

// Admin order management
Route::middleware(['auth', 'verified'])->prefix('admin/orders')->name('admin.orders.')->group(function () {
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
Route::middleware(['auth', 'verified'])->prefix('api/inventory')->name('api.inventory.')->group(function () {
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

require __DIR__.'/auth.php';
