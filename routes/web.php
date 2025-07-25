<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\VendorApplicantController;
use App\Http\Controllers\SupplierController;
use App\Models\YogurtProduct;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Broadcast;

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

// Test mail route
Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from Laravel', function($message) {
            $message->to('test@example.com')
                    ->subject('Test Email');
        });
        return 'Mail sent successfully!';
    } catch (\Exception $e) {
        return 'Mail error: ' . $e->getMessage();
    }
});

// Main dashboard route
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
                    $customerName = $order->customer ? $order->customer->name : 'N/A';
                    $retailerName = $order->retailer ? ($order->retailer->name ?? 'N/A') : 'N/A';
                    return (object) [
                        'id' => $order->id,
                        'order_number' => $order->order_number ?? $order->id,
                        'order_type' => $order->order_type ?? '-',
                        'customer_name' => $customerName,
                        'retailer_name' => $retailerName,
                        'order_date' => $order->order_date ?? null,
                        'created_at' => $order->created_at,
                        'delivery_address' => $order->delivery_address ?? '-',
                        'total_amount' => $order->total_amount ?? $order->total ?? 0,
                        'total' => $order->total ?? 0,
                        'order_status' => $order->order_status ?? $order->status ?? '-',
                        'status' => $order->status ?? '-',
                        'payment_status' => $order->payment_status ?? '-',
                    ];
                });
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
            case 'employee':
                return redirect()->route('dashboard.employee');
            case 'customer':
                return redirect()->route('dashboard.customer');
            default:
                $employeeRecord = \App\Models\Employee::where('user_id', $user->id)->first();
                if ($employeeRecord) {
                    return redirect()->route('dashboard.employee');
                }
                return redirect('/')->with('error', 'Access denied. Please contact administrator.');
        }
    }
    return redirect()->route('login');
})->middleware(['auth'])->name('dashboard');

// Role-specific dashboards
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/retailer', [\App\Http\Controllers\RetailerDashboardController::class, 'index'])->name('dashboard.retailer');
    Route::get('/dashboard/supplier', [\App\Http\Controllers\SupplierController::class, 'supplierDashboard'])->name('dashboard.supplier');
    Route::get('/dashboard/vendor', [\App\Http\Controllers\VendorDashboardController::class, 'showDashboard'])->name('dashboard.vendor');
    Route::get('/dashboard/employee', [\App\Http\Controllers\EmployeeDashboardController::class, 'index'])->name('dashboard.employee');
    // Role-specific employee dashboard routes
    Route::get('/dashboard/employee/production-worker', [\App\Http\Controllers\EmployeeDashboardController::class, 'productionWorkerDashboard'])->name('dashboard.employee.production-worker');
    Route::get('/dashboard/employee/warehouse-staff', [\App\Http\Controllers\EmployeeDashboardController::class, 'warehouseStaffDashboard'])->name('dashboard.employee.warehouse-staff');
    Route::get('/dashboard/employee/driver', [\App\Http\Controllers\EmployeeDashboardController::class, 'driverDashboard'])->name('dashboard.employee.driver');
    Route::get('/dashboard/employee/sales-manager', [\App\Http\Controllers\EmployeeDashboardController::class, 'salesManagerDashboard'])->name('dashboard.employee.sales-manager');
    Route::get('/dashboard/customer', [\App\Http\Controllers\CustomerDashboardController::class, 'index'])->name('dashboard.customer');
    Route::get('/vendor/manage-orders', function () {
        return view('vendor.manage-orders');
    })->name('vendor.manage-orders');
    Route::get('/vendor/manage-products', function () {
        return view('vendor.manage-products');
    })->name('vendor.manage-products');
    Route::post('/retailer/orders', [\App\Http\Controllers\RetailerOrderController::class, 'store'])->name('retailer.orders.store');
    Route::resource('customer/orders', \App\Http\Controllers\CustomerOrderController::class)
        ->names('customer.orders')
        ->only(['index', 'show', 'store']);
    // Warehouse staff and driver actions
    Route::post('/dashboard/employee/order/{orderId}/packed', [\App\Http\Controllers\EmployeeDashboardController::class, 'markOrderPacked'])->name('dashboard.employee.order.packed');
    Route::post('/dashboard/employee/order/{orderId}/shipped', [\App\Http\Controllers\EmployeeDashboardController::class, 'markOrderShipped'])->name('dashboard.employee.order.shipped');
    Route::post('/dashboard/employee/delivery/{deliveryId}/out-for-delivery', [\App\Http\Controllers\EmployeeDashboardController::class, 'markDeliveryOutForDelivery'])->name('dashboard.employee.delivery.out_for_delivery');
    Route::post('/dashboard/employee/delivery/{deliveryId}/delivered', [\App\Http\Controllers\EmployeeDashboardController::class, 'markDeliveryDelivered'])->name('dashboard.employee.delivery.delivered');
});

Route::middleware(['auth'])->group(function () {
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
    Route::put('/{id}', [AdminUserController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
});

// Admin employee store route
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/employees')->name('admin.employees.')->group(function () {
    Route::post('/', [\App\Http\Controllers\AdminEmployeeController::class, 'store'])->name('store');
    Route::get('/{employee}/edit', [\App\Http\Controllers\AdminEmployeeController::class, 'edit'])->name('edit');
    Route::put('/{employee}', [\App\Http\Controllers\AdminEmployeeController::class, 'update'])->name('update');
    Route::delete('/{employee}', [\App\Http\Controllers\AdminEmployeeController::class, 'destroy'])->name('destroy');
});

// Admin order management
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/orders')->name('admin.orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
    Route::put('/{id}', [AdminOrderController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
    // API routes for data
    Route::get('/api/orders-data', [AdminOrderController::class, 'getOrdersData'])->name('api.orders-data');
    Route::get('/api/order-statistics', [AdminOrderController::class, 'getOrderStatistics'])->name('api.order-statistics');
    Route::patch('/{order}/status', [App\Http\Controllers\AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    // Bulk update route
    Route::post('/bulk-update', [AdminOrderController::class, 'bulkUpdate'])->name('bulk-update');
});

// Admin order payment status update
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])
    ->patch('/admin/orders/{id}/update-payment-status', [\App\Http\Controllers\AdminOrderController::class, 'updatePaymentStatus'])->name('admin.orders.updatePaymentStatus');

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
    Route::post('/raw-material-orders/{id}/archive', [\App\Http\Controllers\VendorOrderController::class, 'archiveRawMaterialOrder']);
    Route::post('/raw-material-orders/{id}/unarchive', [\App\Http\Controllers\VendorOrderController::class, 'unarchiveRawMaterialOrder']);
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
    Route::post('/reserve-batch/{id}', [\App\Http\Controllers\VendorProductionController::class, 'reserveBatch'])->name('reserve-batch');
    Route::get('/products/{id}/edit', [\App\Http\Controllers\VendorProductController::class, 'edit'])->name('products.edit');
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
    Route::get('/download/{filename}', [\App\Http\Controllers\AdminReportController::class, 'downloadReport'])->name('download');
});

// Reports API routes
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('api/reports')->name('api.reports.')->group(function () {
    Route::get('/templates', [\App\Http\Controllers\AdminReportController::class, 'getReportTemplates'])->name('templates');
    Route::get('/filters', [\App\Http\Controllers\AdminReportController::class, 'getReportFilters'])->name('filters');
    Route::post('/generate', [\App\Http\Controllers\AdminReportController::class, 'generateCustomReport'])->name('generate');
    Route::post('/export', [\App\Http\Controllers\AdminReportController::class, 'exportReport'])->name('export');
    // Scheduled reports routes
    Route::get('/scheduled', [\App\Http\Controllers\AdminReportController::class, 'getScheduledReports'])->name('scheduled');
    Route::post('/scheduled', [\App\Http\Controllers\AdminReportController::class, 'createScheduledReport'])->name('scheduled.create');
    Route::put('/scheduled/{id}', [\App\Http\Controllers\AdminReportController::class, 'updateScheduledReport'])->name('scheduled.update');
    Route::delete('/scheduled/{id}', [\App\Http\Controllers\AdminReportController::class, 'deleteScheduledReport'])->name('scheduled.delete');
    Route::patch('/scheduled/{id}/toggle', [\App\Http\Controllers\AdminReportController::class, 'toggleScheduledReportStatus'])->name('scheduled.toggle');
    Route::post('/scheduled/{id}/trigger', [\App\Http\Controllers\AdminReportController::class, 'triggerScheduledReport'])->name('scheduled.trigger');
    // Report logs routes
    Route::get('/logs', [\App\Http\Controllers\AdminReportController::class, 'getReportLogs'])->name('logs');
    Route::get('/statistics', [\App\Http\Controllers\AdminReportController::class, 'getReportStatistics'])->name('statistics');
});

// Workforce distribution API for admin dashboard
Route::get('/api/workforce/distribution', [\App\Http\Controllers\AdminWorkforceController::class, 'getWorkforceDistribution'])->name('api.workforce.distribution');

// User and Workforce Management (Tabbed)
Route::get('/admin/users', [\App\Http\Controllers\AdminEmployeeController::class, 'index'])->name('admin.users.index');
Route::post('/admin/employees/{employee}/assign-vendor', [\App\Http\Controllers\AdminEmployeeController::class, 'assignVendor'])->name('admin.employees.assignVendor');
Route::post('/admin/employees', [\App\Http\Controllers\AdminEmployeeController::class, 'store'])->name('admin.employees.store');

// Admin vendor applicant management
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/vendor-applicants')->name('admin.vendor-applicants.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminVendorApplicantController::class, 'index'])->name('index');
    Route::post('/{id}/approve', [\App\Http\Controllers\AdminVendorApplicantController::class, 'approve'])->name('approve');
});

// Supplier Milk Batch Management
Route::post('/supplier/milk-batch', [\App\Http\Controllers\SupplierController::class, 'submitMilkBatch']);
Route::get('/supplier/milk-batch/history', [\App\Http\Controllers\SupplierController::class, 'milkBatchHistory']);
Route::patch('/supplier/milk-batch/{id}/status', [\App\Http\Controllers\SupplierController::class, 'updateMilkBatchStatus']);

// Supplier Raw Material Inventory Blade view
Route::middleware(['auth', 'verified'])->get('/supplier/raw-material-inventory', function () {
    return view('supplier.raw-material-inventory');
})->name('supplier.raw-material-inventory');

// Supplier Raw Material Inventory API
Route::middleware(['auth', 'verified'])->get('/api/supplier/raw-material-inventory', [\App\Http\Controllers\SupplierController::class, 'rawMaterialInventory']);
Route::middleware(['auth', 'verified'])->post('/api/supplier/raw-material-inventory', [\App\Http\Controllers\SupplierController::class, 'storeRawMaterial']);
Route::middleware(['auth', 'verified'])->put('/api/supplier/raw-material-inventory/{id}', [\App\Http\Controllers\SupplierController::class, 'updateRawMaterial']);

// Supplier Add Raw Material Blade view
Route::middleware(['auth', 'verified'])->get('/supplier/raw-material-inventory/add', function () {
    return view('supplier.add-raw-material');
})->name('supplier.add-raw-material');

// Supplier Add Raw Material POST (Blade form)
Route::middleware(['auth', 'verified'])->post('/supplier/raw-material-inventory/add', [\App\Http\Controllers\SupplierController::class, 'storeRawMaterialBlade'])->name('supplier.store-raw-material');

// Supplier Profile Page
Route::middleware(['auth', 'verified'])->get('/supplier/profile', [\App\Http\Controllers\SupplierController::class, 'profile'])->name('supplier.profile');
Route::middleware(['auth', 'verified'])->put('/supplier/profile', [\App\Http\Controllers\SupplierController::class, 'updateProfile'])->name('supplier.profile.update');

// Supplier Manage Orders Page
Route::middleware(['auth', 'verified'])->get('/supplier/manage-orders', function () {
    return view('supplier.manage-orders');
})->name('supplier.manage-orders');

// Supplier Order Management API routes
Route::middleware(['auth', 'verified'])->prefix('api/supplier/orders')->group(function () {
    Route::get('/incoming', [\App\Http\Controllers\SupplierOrderController::class, 'incomingOrders']);
    Route::post('/{id}/confirm', [\App\Http\Controllers\SupplierOrderController::class, 'confirmOrder']);
    Route::post('/{id}/process', [\App\Http\Controllers\SupplierOrderController::class, 'processOrder']);
    Route::post('/{id}/ship', [\App\Http\Controllers\SupplierOrderController::class, 'shipOrder']);
    Route::post('/{id}/deliver', [\App\Http\Controllers\SupplierOrderController::class, 'deliverOrder']);
    Route::post('/{id}/reject', [\App\Http\Controllers\SupplierOrderController::class, 'rejectOrder']);
    Route::get('/stats', [\App\Http\Controllers\SupplierOrderController::class, 'orderStats']);
    Route::post('/raw-material-orders/{id}/archive', [\App\Http\Controllers\SupplierOrderController::class, 'archiveRawMaterialOrder']);
    Route::post('/raw-material-orders/{id}/unarchive', [\App\Http\Controllers\SupplierOrderController::class, 'unarchiveRawMaterialOrder']);
    // Bulk update status for supplier raw material orders
    Route::post('/bulk-update-status', [\App\Http\Controllers\SupplierOrderController::class, 'bulkUpdateStatus']);
});

// Supplier Dashboard
Route::get('/supplier/dashboard', [App\Http\Controllers\SupplierController::class, 'supplierDashboard'])->name('supplier.dashboard');

// Delivery API routes
Route::middleware(['auth', 'verified'])->post('/api/deliveries', [\App\Http\Controllers\DeliveryController::class, 'store']);

// Supplier Delivery Form (for testing/demo)
Route::get('/supplier/delivery-form', function () {
    $user = Auth::user();
    return view('supplier.delivery-form', [
        'order_id' => 123,
        'distribution_center_id' => 1,
        'vendor_id' => 45,
        'vendor_name' => 'Acme Vendor Ltd.',
        'vendor_address' => '123 Main St, Cityville',
        'vendor_phone' => '+1234567890',
        'supplier_id' => $user && $user->supplier ? $user->supplier->id : null,
    ]);
});

// API: Get drivers for a supplier
Route::get('/api/supplier/{supplier}/drivers', function($supplierId) {
    $supplier = \App\Models\Supplier::findOrFail($supplierId);
    return response()->json($supplier->drivers()->get([
        'id', 'name', 'phone', 'license', 'photo', 'vehicle_number', 'email', 'emergency_contact'
    ]));
});

// Supplier driver management UI
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/supplier/drivers', [\App\Http\Controllers\SupplierController::class, 'manageDrivers'])->name('supplier.drivers');
    Route::post('/supplier/drivers', [\App\Http\Controllers\SupplierController::class, 'storeDriver'])->name('supplier.drivers.store');
    Route::post('/supplier/drivers/{driverId}/update', [\App\Http\Controllers\SupplierController::class, 'updateDriver'])->name('supplier.drivers.update');
    Route::post('/supplier/drivers/{driverId}/delete', [\App\Http\Controllers\SupplierController::class, 'deleteDriver'])->name('supplier.drivers.delete');
});

// Supplier Track Deliveries Page
Route::middleware(['auth', 'verified'])->get('/supplier/track-deliveries', [\App\Http\Controllers\SupplierController::class, 'trackDeliveries'])->name('supplier.track-deliveries');

// API: Get all distribution centers
Route::get('/api/distribution-centers', function() {
    return \App\Models\DistributionCenter::select('id', 'center_name', 'center_address')->get();
});

// Vendor Deliveries Dashboard Page
Route::middleware(['auth', 'verified'])->get('/vendor/deliveries', [\App\Http\Controllers\VendorDashboardController::class, 'deliveries'])->name('vendor.deliveries');

// Cart API routes
Route::middleware(['auth'])->get('/api/cart', [\App\Http\Controllers\CartController::class, 'getCart']);
Route::middleware(['auth'])->post('/api/cart', [\App\Http\Controllers\CartController::class, 'saveCart']);
Route::get('/cart/proceed-to-checkout', [CartController::class, 'proceedToCheckout'])->name('cart.proceedToCheckout');
Route::get('/cart/removed-items', [\App\Http\Controllers\CartController::class, 'removedItems'])->name('cart.removedItems');

// Help page route (resolved)
Route::view('/help', 'help.index')->name('help.index');

Route::middleware(['auth'])->get('/api/notifications/unread', function() {
    $user = auth()->user();
    $notifications = $user->unreadNotifications->filter(function($notification) {
        return in_array(class_basename($notification->type), ['OrderPlacedNotification', 'DeliveryNoteNotification']);
    })->values();
    return response()->json($notifications);
});

require __DIR__.'/auth.php';

Route::get('/api/admin/raw-material-orders', [\App\Http\Controllers\AdminOrderController::class, 'allRawMaterialOrders']);
Route::get('/admin/raw-material-orders/export-csv', [\App\Http\Controllers\AdminOrderController::class, 'exportRawMaterialOrdersCsv']);
Route::get('/admin/raw-material-orders/export-pdf', [\App\Http\Controllers\AdminOrderController::class, 'exportRawMaterialOrdersPdf']);

// Vendor Production Batch Management
Route::middleware(['auth', 'verified'])->prefix('vendor/production')->group(function () {
    Route::get('/', [\App\Http\Controllers\VendorProductionController::class, 'index'])->name('vendor.production.index');
    Route::get('/create', [\App\Http\Controllers\VendorProductionController::class, 'create'])->name('vendor.production.create');
    Route::post('/store', [\App\Http\Controllers\VendorProductionController::class, 'store'])->name('vendor.production.store');
});

// Machine Learning Analytics Routes (temporary - for testing)
Route::middleware(['auth', 'verified'])->prefix('api/analytics')->group(function () {
    Route::get('/customer-segmentation', [AnalyticsController::class, 'getCustomerSegmentation']);
    Route::get('/demand-forecast', [AnalyticsController::class, 'getDemandForecast']);
    Route::get('/sales-predictions', [AnalyticsController::class, 'getPredictions']);
    Route::get('/predictions', [AnalyticsController::class, 'getPredictions']); // Added for compatibility
    Route::get('/inventory-optimization', [AnalyticsController::class, 'getInventoryOptimization']);
    Route::get('/risk-assessment', [AnalyticsController::class, 'getRiskAssessment']);
    Route::get('/trend-analysis', [AnalyticsController::class, 'getTrendAnalysis']);
    Route::get('/kpi', [AnalyticsController::class, 'kpi']);
});

// Test route to verify machine learning is working
Route::get('/api/test-ml', function () {
    return response()->json([
        'message' => 'Machine Learning API is working!',
        'timestamp' => now()->toISOString()
    ]);
});

// Temporary test route for retailer segmentation
Route::get('/test-retailer-segmentation', function () {
    try {
        $mlService = app(\App\Services\MachineLearningService::class);
        $segmentation = $mlService->performRetailerSegmentation();
        
        return response()->json([
            'success' => true,
            'segmentation' => $segmentation,
            'test_info' => [
                'timestamp' => now()->toISOString(),
                'retailer_count' => \App\Models\Retailer::count(),
                'order_count' => \App\Models\Order::count()
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Admin Product Management
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/products', [\App\Http\Controllers\AdminProductController::class, 'index'])->name('products.index');
    Route::post('/products/{product}/update', [\App\Http\Controllers\AdminProductController::class, 'update'])->name('products.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    
    // Checkout routes
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
    
    // Test route for checkout (remove in production)
    Route::get('/test-checkout', function() {
        if (!Auth::check()) {
            return 'Please login first';
        }
        $cartItems = \App\Models\CartItem::with('product')->where('user_id', Auth::id())->get();
        return response()->json([
            'user_id' => Auth::id(),
            'cart_items_count' => $cartItems->count(),
            'cart_items' => $cartItems->map(function($item) {
                return [
                    'product_name' => $item->product->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->selling_price
                ];
            })
        ]);
    });
    Route::get('/api/warehouse-summary-stats', [\App\Http\Controllers\EmployeeDashboardController::class, 'warehouseSummaryStats'])->name('warehouse.summary.stats');
    Route::get('/retailer/checkout', [\App\Http\Controllers\RetailerCheckoutController::class, 'index'])->name('retailer.checkout');
    Route::post('/retailer/checkout', [\App\Http\Controllers\RetailerCheckoutController::class, 'store'])->name('retailer.checkout.store');
    Route::get('/retailer/cart', function() {
        $cartItems = \App\Models\CartItem::with('product')->where('user_id', Auth::id())->get()->map(function ($item) {
            return [
                'product' => $item->product,
                'quantity' => $item->quantity,
                'subtotal' => $item->quantity * $item->product->selling_price,
            ];
        });
        $total = $cartItems->sum('subtotal');
        return view('retailer.cart', compact('cartItems', 'total'));
    })->name('retailer.cart.index');
});

// Vendor inventory status ranges
Route::middleware(['auth', 'verified'])->post('/vendor/inventory-status-ranges', [\App\Http\Controllers\VendorDashboardController::class, 'saveInventoryStatusRanges'])->name('vendor.inventory-status-ranges');

Route::get('/retailer/orders/history', [App\Http\Controllers\RetailerOrderHistoryController::class, 'index'])->name('retailer.orders.history');
Route::get('/retailer/offers', [App\Http\Controllers\RetailerOffersController::class, 'index'])->name('retailer.offers');
Route::middleware(['auth', 'verified'])->get('/vendor/production', [\App\Http\Controllers\VendorProductionController::class, 'index'])->name('vendor.production.index');

Route::middleware(['auth'])->get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat');
Route::middleware(['auth'])->get('/chat/recipients', [\App\Http\Controllers\ChatController::class, 'getRecipients']);
Route::middleware(['auth'])->get('/chat/unread-counts', [\App\Http\Controllers\ChatController::class, 'getUnreadCountsPerUser']);
Route::middleware(['auth'])->get('/chat/unread-grouped', [\App\Http\Controllers\ChatController::class, 'getUnreadMessagesGroupedBySender']);
Route::middleware(['auth'])->get('/chat/background', [\App\Http\Controllers\ChatController::class, 'getChatBackground']);
Route::middleware(['auth'])->post('/chat/background', [\App\Http\Controllers\ChatController::class, 'setChatBackground']);
Route::middleware(['auth'])->get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'getMessages']);
Route::middleware(['auth'])->post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage']);

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::get('/terms-of-use', function () {
    return view('terms-of-use');
})->name('terms.use');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin/distribution-centers')->name('admin.distribution-centers.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminDistributionCenterController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\AdminDistributionCenterController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\AdminDistributionCenterController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\AdminDistributionCenterController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\AdminDistributionCenterController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\AdminDistributionCenterController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])
    ->get('/admin/distribution-centers/{id}/vendor-inventory-stats', [\App\Http\Controllers\AdminDistributionCenterController::class, 'vendorInventoryStats']);

// Add this route for flat inventory stats by product (not grouped by vendor)
Route::middleware(['auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])
    ->get('/admin/distribution-centers/{id}/inventory-stats', [\App\Http\Controllers\AdminDistributionCenterController::class, 'inventoryStats']);

Route::post('/driver/orders/{order}/proof', [\App\Http\Controllers\DriverOrderController::class, 'uploadProof'])->name('driver.orders.proof');
Route::post('/vendor/orders/{order}/assign-driver', [\App\Http\Controllers\VendorOrderController::class, 'assignDriver'])->name('vendor.orders.assignDriver');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/vendor/assign-driver', [\App\Http\Controllers\VendorOrderController::class, 'showAssignDriverForm'])->name('vendor.assign-driver');
});

Route::middleware(['auth', 'verified'])->get('/vendor/reports', [\App\Http\Controllers\VendorDashboardController::class, 'reportsPage'])->name('vendor.reports');
// Supplier reports page (web)
Route::middleware(['auth', 'verified'])->get('/supplier/reports', [\App\Http\Controllers\SupplierDashboardController::class, 'reportsPage'])->name('supplier.reports');
// Supplier reports API (for AJAX/fetching report data)
Route::middleware(['auth', 'verified'])->get('/supplier/my-reports', [\App\Http\Controllers\SupplierDashboardController::class, 'myReports'])->name('supplier.my-reports');

Route::get('/admin/deliveries/{delivery}', [App\Http\Controllers\AdminDeliveryController::class, 'show'])->name('admin.deliveries.show');
Route::middleware(['auth'])->get('/driver/assigned-deliveries', [\App\Http\Controllers\DriverDashboardApiController::class, 'assignedDeliveries'])->name('driver.assigned-deliveries');

// Bulk update delivery status (admin)
Route::post('/admin/deliveries/bulk-update-status', [\App\Http\Controllers\AdminDeliveryController::class, 'bulkUpdateStatus'])->name('admin.deliveries.bulk-update-status');

// Admin API: Driver delivery loads graph
Route::get('/api/admin/driver-delivery-loads', [\App\Http\Controllers\AdminDeliveryController::class, 'driverDeliveryLoads'])->name('api.admin.driver-delivery-loads');

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/fetch', [App\Http\Controllers\NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
});

Broadcast::routes();

// Admin User Management Routes with AJAX support
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Main user management page
    Route::get('/user-management', [App\Http\Controllers\AdminUserController::class, 'index'])->name('user-management.index');
    
    // AJAX routes for tab content reloading
    Route::get('/user-management/users/content', [App\Http\Controllers\AdminUserController::class, 'getUsersContent'])->name('user-management.users.content');
    Route::get('/user-management/workforce/content', [App\Http\Controllers\AdminUserController::class, 'getWorkforceContent'])->name('user-management.workforce.content');
    Route::get('/user-management/distribution-centers/content', [App\Http\Controllers\AdminUserController::class, 'getDistributionCentersContent'])->name('user-management.distribution-centers.content');
    Route::get('/user-management/deliveries/content', [App\Http\Controllers\AdminUserController::class, 'getDeliveriesContent'])->name('user-management.deliveries.content');
    
    // AJAX CRUD routes for users
    Route::post('/users', [App\Http\Controllers\AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [App\Http\Controllers\AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminUserController::class, 'destroy'])->name('users.destroy');
    
    // AJAX CRUD routes for workforce
    Route::post('/employees', [App\Http\Controllers\AdminUserController::class, 'storeEmployee'])->name('employees.store');
    Route::delete('/employees/{employee}', [App\Http\Controllers\AdminUserController::class, 'destroyEmployee'])->name('employees.destroy');
    
    // AJAX CRUD routes for distribution centers
    Route::post('/distribution-centers', [App\Http\Controllers\AdminUserController::class, 'storeDistributionCenter'])->name('distribution-centers.store');
    Route::delete('/distribution-centers/{distributionCenter}', [App\Http\Controllers\AdminUserController::class, 'destroyDistributionCenter'])->name('distribution-centers.destroy');
    
    // AJAX routes for deliveries
    Route::put('/deliveries/{delivery}/status', [App\Http\Controllers\AdminUserController::class, 'updateDeliveryStatus'])->name('deliveries.update-status');
});

Route::middleware(['auth', 'verified'])->get('/supplier/raw-material-orders/{id}', [\App\Http\Controllers\SupplierOrderController::class, 'show'])->name('supplier.raw-material-orders.show');
Route::middleware(['auth', 'verified'])->patch('/supplier/raw-material-orders/{id}/update-payment-status', [\App\Http\Controllers\SupplierOrderController::class, 'updatePaymentStatus'])->name('supplier.raw-material-orders.updatePaymentStatus');
