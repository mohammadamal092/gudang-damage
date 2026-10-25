<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DamageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\WarehouseLocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('locations', WarehouseLocationController::class)->except('show');
    Route::resource('items', ItemController::class)->except('show');

    // Rute khusus harus didaftarkan SEBELUM resource agar tidak tertangkap {damage}.
    Route::get('damages/export', [DamageController::class, 'export'])->name('damages.export');
    Route::get('damages/{damage}/print', [DamageController::class, 'print'])->name('damages.print');
    Route::patch('damages/{damage}/status', [DamageController::class, 'updateStatus'])->name('damages.status');
    Route::resource('damages', DamageController::class);
});
