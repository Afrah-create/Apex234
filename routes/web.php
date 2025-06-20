<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard/admin', function () {
    return view('dashboard-admin');
})->middleware(['auth'])->name('dashboard.admin');

Route::get('/dashboard/supplier', function () {
    return view('dashboard-supplier');
})->middleware(['auth'])->name('dashboard.supplier');

Route::get('/dashboard/retailer', function () {
    return view('dashboard-retailer');
})->middleware(['auth'])->name('dashboard.retailer');

Route::get('/dashboard/vendor', function () {
    return view('dashboard-vendor');
})->middleware(['auth'])->name('dashboard.vendor');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');
