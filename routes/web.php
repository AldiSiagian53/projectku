<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;

// Login Routes (public)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Hidden admin access activation (only from login page)
Route::post('/admin/activate', [App\Http\Controllers\AdminController::class, 'activate'])->name('admin.activate');

// Admin Login Routes (public but requires hidden key activation)
Route::get('/admin/login', [App\Http\Controllers\AdminLoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\AdminLoginController::class, 'adminLogin']);

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Protected Routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
    // routes/web.php
    Route::get('/alerts', [App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');
    
    // Settings Routes
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    
    // Admin Routes
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/users', [App\Http\Controllers\AdminController::class, 'store'])->name('admin.store');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('admin.destroy');
});