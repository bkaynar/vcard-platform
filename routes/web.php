<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', fn() => Inertia::render('Admin/Dashboard'))->name('admin.dashboard')
        ->middleware('role:admin');

    Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))->name('dashboard');

    Route::get('/user/dashboard', fn() => Inertia::render('User/Dashboard'))->name('user.dashboard')
        ->middleware('role:user');

    Route::resource('/admin/users', UserController::class)->names('admin.users');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
