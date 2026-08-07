<?php

use App\Http\Controllers\SuperAdmin\AnalyticsController;
use App\Http\Controllers\SuperAdmin\AuditLogController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\MonitoringController;
use App\Http\Controllers\SuperAdmin\PaymentController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| Semua route di sini memerlukan autentikasi, email verified,
| dan role super_admin.
|
*/

Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', DashboardController::class)
            ->name('dashboard');

        // Tenant Management
        Route::resource('tenants', TenantController::class);
        Route::post('/tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])
            ->name('tenants.toggle-status');

        // Payment Verification
        Route::get('/payments', [PaymentController::class, 'index'])
            ->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])
            ->name('payments.show');
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])
            ->name('payments.approve');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])
            ->name('payments.reject');

        // Plan Management
        Route::resource('plans', PlanController::class)->except(['show']);

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        // Analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])
            ->name('analytics.index');

        // Monitoring
        Route::get('/monitoring', [MonitoringController::class, 'index'])
            ->name('monitoring.index');
    });
