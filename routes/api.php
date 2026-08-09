<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\DiaryController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FamilyMemberController;
use App\Http\Controllers\Api\GrowthController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\SuperAdmin\AnalyticsController;
use App\Http\Controllers\Api\SuperAdmin\MonitoringController;
use App\Http\Controllers\Api\SuperAdmin\PaymentController;
use App\Http\Controllers\Api\SuperAdmin\PlanController;
use App\Http\Controllers\Api\SuperAdmin\PluginController as SuperAdminPluginController;
use App\Http\Controllers\Api\SuperAdmin\TenantController;
use App\Http\Controllers\Api\TenantAdmin\DomainApiController;
use App\Http\Controllers\Api\TenantAdmin\EnterpriseApiController;
use App\Http\Controllers\Api\TenantAdmin\PluginController as TenantAdminPluginController;
use App\Http\Controllers\Api\TenantAdmin\TenantAdminController;
use App\Http\Controllers\Api\TenantAdmin\WhiteLabelController;
use App\Http\Controllers\Api\TimelineController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| ForMysha REST API routes. All routes are prefixed with /api/v1.
|
|--------------------------------------------------------------------------
*/

// Rate Limiting Configuration
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

RateLimiter::for('upload', function (Request $request) {
    return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
});

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Routes (No Authentication Required)
    |--------------------------------------------------------------------------
    */

    Route::middleware('throttle:auth')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
    });

    // Plans (Public)
    Route::get('/plans', [PlanApiController::class, 'index'])->name('api.plans.index');
    Route::get('/plans/{plan}', [PlanApiController::class, 'show'])->name('api.plans.show');

    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Authentication Required)
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('throttle:api')->group(function () {
            // Profile
            Route::get('/me', [AuthController::class, 'me'])->name('api.me');
            Route::put('/me', [AuthController::class, 'updateProfile'])->name('api.me.update');

            // Auth
            Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
            Route::put('/auth/password', [AuthController::class, 'updatePassword'])->name('api.auth.password');
            Route::delete('/auth/account', [AuthController::class, 'destroy'])->name('api.auth.destroy');

            // Children
            Route::get('/children', [ChildController::class, 'index'])->name('api.children.index');
            Route::post('/children', [ChildController::class, 'store'])->name('api.children.store');
            Route::get('/children/{child}', [ChildController::class, 'show'])->name('api.children.show');
            Route::put('/children/{child}', [ChildController::class, 'update'])->name('api.children.update');
            Route::delete('/children/{child}', [ChildController::class, 'destroy'])->name('api.children.destroy');

            // Timelines
            Route::get('/children/{child}/timelines', [TimelineController::class, 'index'])->name('api.timelines.index');
            Route::post('/children/{child}/timelines', [TimelineController::class, 'store'])->name('api.timelines.store');
            Route::get('/children/{child}/timelines/{timeline}', [TimelineController::class, 'show'])->name('api.timelines.show');
            Route::put('/children/{child}/timelines/{timeline}', [TimelineController::class, 'update'])->name('api.timelines.update');
            Route::delete('/children/{child}/timelines/{timeline}', [TimelineController::class, 'destroy'])->name('api.timelines.destroy');

            // Albums
            Route::get('/children/{child}/albums', [AlbumController::class, 'index'])->name('api.albums.index');
            Route::post('/children/{child}/albums', [AlbumController::class, 'store'])->name('api.albums.store');
            Route::get('/children/{child}/albums/{album}', [AlbumController::class, 'show'])->name('api.albums.show');
            Route::put('/children/{child}/albums/{album}', [AlbumController::class, 'update'])->name('api.albums.update');
            Route::delete('/children/{child}/albums/{album}', [AlbumController::class, 'destroy'])->name('api.albums.destroy');

            // Media (with upload rate limiting)
            Route::get('/children/{child}/media', [MediaController::class, 'index'])->name('api.media.index');
            Route::post('/children/{child}/media', [MediaController::class, 'store'])->middleware('throttle:upload')->name('api.media.store');
            Route::delete('/children/{child}/media/{media}', [MediaController::class, 'destroy'])->name('api.media.destroy');

            // Diaries
            Route::get('/children/{child}/diaries', [DiaryController::class, 'index'])->name('api.diaries.index');
            Route::post('/children/{child}/diaries', [DiaryController::class, 'store'])->name('api.diaries.store');
            Route::get('/children/{child}/diaries/{diary}', [DiaryController::class, 'show'])->name('api.diaries.show');
            Route::put('/children/{child}/diaries/{diary}', [DiaryController::class, 'update'])->name('api.diaries.update');
            Route::delete('/children/{child}/diaries/{diary}', [DiaryController::class, 'destroy'])->name('api.diaries.destroy');

            // Documents
            Route::get('/children/{child}/documents', [DocumentController::class, 'index'])->name('api.documents.index');
            Route::post('/children/{child}/documents', [DocumentController::class, 'store'])->name('api.documents.store');
            Route::get('/children/{child}/documents/{document}', [DocumentController::class, 'show'])->name('api.documents.show');
            Route::put('/children/{child}/documents/{document}', [DocumentController::class, 'update'])->name('api.documents.update');
            Route::delete('/children/{child}/documents/{document}', [DocumentController::class, 'destroy'])->name('api.documents.destroy');

            // Events
            Route::get('/children/{child}/events', [EventController::class, 'index'])->name('api.events.index');
            Route::post('/children/{child}/events', [EventController::class, 'store'])->name('api.events.store');
            Route::get('/children/{child}/events/{event}', [EventController::class, 'show'])->name('api.events.show');
            Route::put('/children/{child}/events/{event}', [EventController::class, 'update'])->name('api.events.update');
            Route::delete('/children/{child}/events/{event}', [EventController::class, 'destroy'])->name('api.events.destroy');

            // Growths
            Route::get('/children/{child}/growths', [GrowthController::class, 'index'])->name('api.growths.index');
            Route::post('/children/{child}/growths', [GrowthController::class, 'store'])->name('api.growths.store');
            Route::get('/children/{child}/growths/chart', [GrowthController::class, 'chartData'])->name('api.growths.chart');
            Route::get('/children/{child}/growths/{growth}', [GrowthController::class, 'show'])->name('api.growths.show');
            Route::put('/children/{child}/growths/{growth}', [GrowthController::class, 'update'])->name('api.growths.update');
            Route::delete('/children/{child}/growths/{growth}', [GrowthController::class, 'destroy'])->name('api.growths.destroy');

            // Health Records
            Route::get('/children/{child}/health-records', [HealthRecordController::class, 'index'])->name('api.health-records.index');
            Route::post('/children/{child}/health-records', [HealthRecordController::class, 'store'])->name('api.health-records.store');
            Route::get('/children/{child}/health-records/{healthRecord}', [HealthRecordController::class, 'show'])->name('api.health-records.show');
            Route::put('/children/{child}/health-records/{healthRecord}', [HealthRecordController::class, 'update'])->name('api.health-records.update');
            Route::delete('/children/{child}/health-records/{healthRecord}', [HealthRecordController::class, 'destroy'])->name('api.health-records.destroy');

            // Family Members
            Route::get('/children/{child}/family-members', [FamilyMemberController::class, 'index'])->name('api.family-members.index');
            Route::post('/children/{child}/family-members', [FamilyMemberController::class, 'store'])->name('api.family-members.store');
            Route::get('/children/{child}/family-members/{familyMember}', [FamilyMemberController::class, 'show'])->name('api.family-members.show');
            Route::put('/children/{child}/family-members/{familyMember}', [FamilyMemberController::class, 'update'])->name('api.family-members.update');
            Route::delete('/children/{child}/family-members/{familyMember}', [FamilyMemberController::class, 'destroy'])->name('api.family-members.destroy');

            // Notifications
            Route::get('/notifications', [NotificationApiController::class, 'index'])->name('api.notifications.index');
            Route::get('/notifications/unread-count', [NotificationApiController::class, 'unreadCount'])->name('api.notifications.unread-count');
            Route::post('/notifications/{notification}/read', [NotificationApiController::class, 'markAsRead'])->name('api.notifications.read');
            Route::post('/notifications/read-all', [NotificationApiController::class, 'markAllAsRead'])->name('api.notifications.read-all');

            // Search
            Route::get('/search', [SearchController::class, 'index'])->name('api.search');

            // Dashboard
            Route::get('/dashboard', [DashboardApiController::class, 'index'])->name('api.dashboard');
        });

        /*
        |--------------------------------------------------------------------------
        | Super Admin API Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin')->prefix('admin')->group(function () {
            // Tenants
            Route::get('/tenants', [TenantController::class, 'index'])->name('api.admin.tenants.index');
            Route::post('/tenants', [TenantController::class, 'store'])->name('api.admin.tenants.store');
            Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('api.admin.tenants.show');
            Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('api.admin.tenants.update');
            Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('api.admin.tenants.destroy');
            Route::post('/tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('api.admin.tenants.toggle-status');

            // Payments
            Route::get('/payments', [PaymentController::class, 'index'])->name('api.admin.payments.index');
            Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('api.admin.payments.show');
            Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('api.admin.payments.approve');
            Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('api.admin.payments.reject');

            // Plans
            Route::get('/plans', [PlanController::class, 'index'])->name('api.admin.plans.index');
            Route::post('/plans', [PlanController::class, 'store'])->name('api.admin.plans.store');
            Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('api.admin.plans.show');
            Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('api.admin.plans.update');
            Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('api.admin.plans.destroy');

            // Analytics & Monitoring
            Route::get('/analytics', [AnalyticsController::class, 'index'])->name('api.admin.analytics');
            Route::get('/monitoring', [MonitoringController::class, 'index'])->name('api.admin.monitoring');

            // Plugin Management
            Route::get('/plugins', [SuperAdminPluginController::class, 'index'])->name('api.admin.plugins.index');
            Route::post('/plugins', [SuperAdminPluginController::class, 'store'])->name('api.admin.plugins.store');
            Route::get('/plugins/{plugin}', [SuperAdminPluginController::class, 'show'])->name('api.admin.plugins.show');
            Route::put('/plugins/{plugin}', [SuperAdminPluginController::class, 'update'])->name('api.admin.plugins.update');
            Route::delete('/plugins/{plugin}', [SuperAdminPluginController::class, 'destroy'])->name('api.admin.plugins.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Subscription API Routes
        |--------------------------------------------------------------------------
        */

        Route::prefix('subscription')->group(function () {
            Route::get('/current', [SubscriptionApiController::class, 'current'])->name('api.subscription.current');
            Route::get('/history', [SubscriptionApiController::class, 'history'])->name('api.subscription.history');
            Route::post('/subscribe/{plan}', [SubscriptionApiController::class, 'subscribe'])->name('api.subscription.subscribe');
            Route::post('/payment', [SubscriptionApiController::class, 'uploadPayment'])->name('api.subscription.payment');
        });

        /*
        |--------------------------------------------------------------------------
        | Tenant Admin API Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:tenant_admin')->prefix('tenant-admin')->group(function () {
            Route::get('/dashboard', [TenantAdminController::class, 'dashboard'])->name('api.tenant-admin.dashboard');
            Route::put('/branding', [TenantAdminController::class, 'updateBranding'])->name('api.tenant-admin.branding');
            Route::put('/settings', [TenantAdminController::class, 'updateSettings'])->name('api.tenant-admin.settings');
            Route::get('/usage', [TenantAdminController::class, 'usage'])->name('api.tenant-admin.usage');

            // White Label / Branding Advanced
            Route::get('/branding', [WhiteLabelController::class, 'getBranding'])->name('api.tenant-admin.branding.get');
            Route::put('/branding/advanced', [WhiteLabelController::class, 'updateBranding'])->name('api.tenant-admin.branding.advanced');
            Route::post('/branding/favicon', [WhiteLabelController::class, 'uploadFavicon'])->name('api.tenant-admin.branding.favicon');
            Route::get('/branding/preview', [WhiteLabelController::class, 'getCssPreview'])->name('api.tenant-admin.branding.preview');

            // Custom Domain
            Route::get('/domain', [DomainApiController::class, 'show'])->name('api.tenant-admin.domain.show');
            Route::put('/domain', [DomainApiController::class, 'update'])->name('api.tenant-admin.domain.update');
            Route::post('/domain/verify', [DomainApiController::class, 'verify'])->name('api.tenant-admin.domain.verify');
            Route::delete('/domain', [DomainApiController::class, 'remove'])->name('api.tenant-admin.domain.remove');

            // Plugin Marketplace
            Route::get('/plugins', [TenantAdminPluginController::class, 'index'])->name('api.tenant-admin.plugins.index');
            Route::post('/plugins/{plugin}/install', [TenantAdminPluginController::class, 'install'])->name('api.tenant-admin.plugins.install');
            Route::delete('/plugins/{plugin}/uninstall', [TenantAdminPluginController::class, 'uninstall'])->name('api.tenant-admin.plugins.uninstall');
            Route::post('/plugins/{plugin}/toggle', [TenantAdminPluginController::class, 'toggle'])->name('api.tenant-admin.plugins.toggle');
            Route::get('/plugins/{plugin}/settings', [TenantAdminPluginController::class, 'settings'])->name('api.tenant-admin.plugins.settings');
            Route::put('/plugins/{plugin}/settings', [TenantAdminPluginController::class, 'updateSettings'])->name('api.tenant-admin.plugins.settings.update');

            // Enterprise
            Route::get('/enterprise/analytics', [EnterpriseApiController::class, 'getAnalytics'])->name('api.tenant-admin.enterprise.analytics');
            Route::get('/enterprise/analytics/{metric}', [EnterpriseApiController::class, 'getMetrics'])->name('api.tenant-admin.enterprise.metrics');
            Route::get('/enterprise/invitations', [EnterpriseApiController::class, 'listInvitations'])->name('api.tenant-admin.enterprise.invitations');
            Route::post('/enterprise/invitations', [EnterpriseApiController::class, 'invite'])->name('api.tenant-admin.enterprise.invite');
            Route::delete('/enterprise/invitations/{invitation}', [EnterpriseApiController::class, 'revokeInvitation'])->name('api.tenant-admin.enterprise.revoke-invitation');
            Route::get('/enterprise/import-jobs', [EnterpriseApiController::class, 'getImportJobs'])->name('api.tenant-admin.enterprise.import-jobs');
        });

        /*
        |--------------------------------------------------------------------------
        | Webhook API Routes
        |--------------------------------------------------------------------------
        */

        Route::prefix('webhooks')->group(function () {
            Route::get('/', [WebhookController::class, 'index'])->name('api.webhooks.index');
            Route::post('/', [WebhookController::class, 'store'])->name('api.webhooks.store');
            Route::get('/{webhook}', [WebhookController::class, 'show'])->name('api.webhooks.show');
            Route::put('/{webhook}', [WebhookController::class, 'update'])->name('api.webhooks.update');
            Route::delete('/{webhook}', [WebhookController::class, 'destroy'])->name('api.webhooks.destroy');
            Route::post('/{webhook}/test', [WebhookController::class, 'test'])->name('api.webhooks.test');
            Route::get('/{webhook}/logs', [WebhookController::class, 'logs'])->name('api.webhooks.logs');
        });
    });

});
