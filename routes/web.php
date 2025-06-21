<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')
        ->middleware('role:admin');

    Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))->name('dashboard');

    Route::get('/user/dashboard', fn() => Inertia::render('User/Dashboard'))->name('user.dashboard')
        ->middleware('role:user');

    Route::resource('/admin/users', UserController::class)->names('admin.users');

    // Admin Dashboard ve Eylemler
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('admin.dashboard.clear-cache');
    });

    // Sistem Ayarları Route'ları
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/settings', [SystemSettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings', [SystemSettingController::class, 'update'])->name('admin.settings.update');
        Route::post('/admin/settings/test-smtp', [SystemSettingController::class, 'testSmtp'])->name('admin.settings.test-smtp');
        Route::post('/admin/settings/test-shopify', [SystemSettingController::class, 'testShopify'])->name('admin.settings.test-shopify');
        Route::post('/admin/settings/clear-cache', [SystemSettingController::class, 'clearCache'])->name('admin.settings.clear-cache');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
