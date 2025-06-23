<?php

use App\Http\Controllers\ProfileController;
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
    Route::get('/{id}/edit', [AdminUserController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [AdminUserController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/auth.php';
