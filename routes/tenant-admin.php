<?php

use App\Http\Controllers\TenantAdmin\BrandingController;
use App\Http\Controllers\TenantAdmin\SettingsController;
use App\Http\Controllers\TenantAdmin\TenantAdminController;
use App\Http\Controllers\TenantAdmin\UsageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Admin Routes
|--------------------------------------------------------------------------
|
| Semua route di sini memerlukan autentikasi, email verified,
| dan role tenant_admin. Panel ini untuk mengelola pengaturan
| tenant seperti branding, settings, dan usage.
|
*/

Route::middleware(['auth', 'verified', 'role:tenant_admin'])
    ->prefix('admin')
    ->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [TenantAdminController::class, 'dashboard'])
            ->name('dashboard');

        // Branding
        Route::get('/branding', [BrandingController::class, 'edit'])
            ->name('branding.edit');
        Route::put('/branding', [BrandingController::class, 'update'])
            ->name('branding.update');

        // Settings
        Route::get('/settings', [SettingsController::class, 'edit'])
            ->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])
            ->name('settings.update');

        // Usage
        Route::get('/usage', [UsageController::class, 'index'])
            ->name('usage.index');
    });
