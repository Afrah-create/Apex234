<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
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

require __DIR__.'/auth.php';
