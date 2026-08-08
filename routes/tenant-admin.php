<?php

use App\Http\Controllers\TenantAdmin\BrandingController;
use App\Http\Controllers\TenantAdmin\DomainController;
use App\Http\Controllers\TenantAdmin\EnterpriseController;
use App\Http\Controllers\TenantAdmin\PluginController;
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

        // Advanced Branding
        Route::put('/branding/advanced', [BrandingController::class, 'updateAdvanced'])
            ->name('branding.advanced.update');

        // Custom Domain
        Route::get('/domain', [DomainController::class, 'index'])->name('domain.index');
        Route::put('/domain', [DomainController::class, 'update'])->name('domain.update');
        Route::post('/domain/verify', [DomainController::class, 'verify'])->name('domain.verify');
        Route::delete('/domain', [DomainController::class, 'remove'])->name('domain.remove');

        // Plugin Marketplace
        Route::get('/plugins', [PluginController::class, 'index'])->name('plugins.index');
        Route::post('/plugins/{plugin}/install', [PluginController::class, 'install'])->name('plugins.install');
        Route::delete('/plugins/{plugin}/uninstall', [PluginController::class, 'uninstall'])->name('plugins.uninstall');
        Route::post('/plugins/{plugin}/toggle', [PluginController::class, 'toggle'])->name('plugins.toggle');
        Route::get('/plugins/{plugin}/settings', [PluginController::class, 'settings'])->name('plugins.settings');
        Route::put('/plugins/{plugin}/settings', [PluginController::class, 'updateSettings'])->name('plugins.settings.update');

        // Enterprise
        Route::get('/enterprise/analytics', [EnterpriseController::class, 'analytics'])->name('enterprise.analytics');
        Route::get('/enterprise/invitations', [EnterpriseController::class, 'invitations'])->name('enterprise.invitations');
        Route::post('/enterprise/invitations', [EnterpriseController::class, 'invite'])->name('enterprise.invite');
        Route::delete('/enterprise/invitations/{invitation}', [EnterpriseController::class, 'revokeInvitation'])->name('enterprise.revoke-invitation');
        Route::get('/enterprise/import', [EnterpriseController::class, 'import'])->name('enterprise.import');
        Route::post('/enterprise/import', [EnterpriseController::class, 'processImport'])->name('enterprise.process-import');
    });
