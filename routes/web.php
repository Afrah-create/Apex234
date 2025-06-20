<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Auth::routes();

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
    Route::patch('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
    Route::patch('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
    Route::get('/supplier-dashboard', [App\Http\Controllers\SupplierDashboardController::class, 'index'])->name('supplier.dashboard');
    Route::get('/customer-dashboard', [App\Http\Controllers\CustomerDashboardController::class, 'index'])->name('customer.dashboard');
});

// Catch all route (moved outside middleware group)
Route::get('{page}', ['as' => 'page.index', 'uses' => 'App\Http\Controllers\PageController@index']);

Route::get('/admin/dashboard', function () {
    return view('admin_dashboard');
})->name('admin.dashboard');

Route::get('/general/dashboard', function () {
    return view('general_dashboard');
})->name('general.dashboard');

Route::get('/supplier/dashboard', function () {
    return view('supplier_dashboard');
})->name('supplier.dashboard');

Route::get('/customer/dashboard', function () {
    return view('customer_dashboard');
})->name('customer.dashboard');

