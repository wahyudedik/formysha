<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\GrowthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TimelineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Static pages
Route::get('/tentang-kami', [PageController::class, 'about'])->name('pages.about');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('pages.terms');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Children routes (CRUD without {child} parameter)
    Route::resource('children', ChildController::class)->except(['edit', 'show']);

    // All routes with {child} parameter — protected by child.ownership middleware
    Route::middleware('child.ownership')->group(function () {

        // Child show/edit (explicit routes for proper URL generation)
        Route::get('/children/{child}', [ChildController::class, 'show'])->name('children.show');
        Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');

        // Family member routes (nested under children)
        Route::get('/children/{child}/family', [FamilyMemberController::class, 'index'])->name('family.index');
        Route::get('/children/{child}/family/create', [FamilyMemberController::class, 'create'])->name('family.create');
        Route::post('/children/{child}/family', [FamilyMemberController::class, 'store'])->name('family.store');
        Route::get('/children/{child}/family/{familyMember}/edit', [FamilyMemberController::class, 'edit'])->name('family.edit');
        Route::put('/children/{child}/family/{familyMember}', [FamilyMemberController::class, 'update'])->name('family.update');
        Route::delete('/children/{child}/family/{familyMember}', [FamilyMemberController::class, 'destroy'])->name('family.destroy');

        // Timeline routes (nested under children)
        Route::get('/children/{child}/timeline', [TimelineController::class, 'index'])->name('timeline.index');
        Route::get('/children/{child}/timeline/create', [TimelineController::class, 'create'])->name('timeline.create');
        Route::post('/children/{child}/timeline', [TimelineController::class, 'store'])->name('timeline.store');
        Route::get('/children/{child}/timeline/{timeline}', [TimelineController::class, 'show'])->name('timeline.show');
        Route::get('/children/{child}/timeline/{timeline}/edit', [TimelineController::class, 'edit'])->name('timeline.edit');
        Route::put('/children/{child}/timeline/{timeline}', [TimelineController::class, 'update'])->name('timeline.update');
        Route::delete('/children/{child}/timeline/{timeline}', [TimelineController::class, 'destroy'])->name('timeline.destroy');

        // Album routes (nested under children)
        Route::get('/children/{child}/albums', [AlbumController::class, 'index'])->name('albums.index');
        Route::get('/children/{child}/albums/create', [AlbumController::class, 'create'])->name('albums.create');
        Route::post('/children/{child}/albums', [AlbumController::class, 'store'])->name('albums.store');
        Route::get('/children/{child}/albums/{album}', [AlbumController::class, 'show'])->name('albums.show');
        Route::get('/children/{child}/albums/{album}/edit', [AlbumController::class, 'edit'])->name('albums.edit');
        Route::put('/children/{child}/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
        Route::delete('/children/{child}/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');

        // Diary routes (nested under children)
        Route::get('/children/{child}/diaries', [DiaryController::class, 'index'])->name('diaries.index');
        Route::get('/children/{child}/diaries/create', [DiaryController::class, 'create'])->name('diaries.create');
        Route::post('/children/{child}/diaries', [DiaryController::class, 'store'])->name('diaries.store');
        Route::get('/children/{child}/diaries/{diary}', [DiaryController::class, 'show'])->name('diaries.show');
        Route::get('/children/{child}/diaries/{diary}/edit', [DiaryController::class, 'edit'])->name('diaries.edit');
        Route::put('/children/{child}/diaries/{diary}', [DiaryController::class, 'update'])->name('diaries.update');
        Route::delete('/children/{child}/diaries/{diary}', [DiaryController::class, 'destroy'])->name('diaries.destroy');

        // Document routes (nested under children)
        Route::get('/children/{child}/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/children/{child}/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/children/{child}/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/children/{child}/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/children/{child}/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/children/{child}/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/children/{child}/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // Calendar routes (nested under children)
        Route::get('/children/{child}/calendar', [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/children/{child}/calendar/create', [CalendarController::class, 'create'])->name('calendar.create');
        Route::post('/children/{child}/calendar', [CalendarController::class, 'store'])->name('calendar.store');
        Route::get('/children/{child}/calendar/{event}', [CalendarController::class, 'show'])->name('calendar.show');
        Route::get('/children/{child}/calendar/{event}/edit', [CalendarController::class, 'edit'])->name('calendar.edit');
        Route::put('/children/{child}/calendar/{event}', [CalendarController::class, 'update'])->name('calendar.update');
        Route::delete('/children/{child}/calendar/{event}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

        // Growth routes (nested under children)
        Route::get('/children/{child}/growth', [GrowthController::class, 'index'])->name('growth.index');
        Route::get('/children/{child}/growth/create', [GrowthController::class, 'create'])->name('growth.create');
        Route::post('/children/{child}/growth', [GrowthController::class, 'store'])->name('growth.store');
        Route::get('/children/{child}/growth/{growth}/edit', [GrowthController::class, 'edit'])->name('growth.edit');
        Route::put('/children/{child}/growth/{growth}', [GrowthController::class, 'update'])->name('growth.update');
        Route::delete('/children/{child}/growth/{growth}', [GrowthController::class, 'destroy'])->name('growth.destroy');

        // Health records routes (nested under children)
        Route::get('/children/{child}/health', [HealthController::class, 'index'])->name('health.index');
        Route::get('/children/{child}/health/create', [HealthController::class, 'create'])->name('health.create');
        Route::post('/children/{child}/health', [HealthController::class, 'store'])->name('health.store');
        Route::get('/children/{child}/health/{healthRecord}', [HealthController::class, 'show'])->name('health.show');
        Route::get('/children/{child}/health/{healthRecord}/edit', [HealthController::class, 'edit'])->name('health.edit');
        Route::put('/children/{child}/health/{healthRecord}', [HealthController::class, 'update'])->name('health.update');
        Route::delete('/children/{child}/health/{healthRecord}', [HealthController::class, 'destroy'])->name('health.destroy');

        // Export routes (nested under children) — throttled to prevent abuse
        Route::get('/children/{child}/export/profile', [ExportController::class, 'childProfile'])->name('export.profile')->middleware('throttle:5,1');
        Route::get('/children/{child}/export/health', [ExportController::class, 'healthRecords'])->name('export.health')->middleware('throttle:5,1');
        Route::get('/children/{child}/export/growth', [ExportController::class, 'growthRecords'])->name('export.growth')->middleware('throttle:5,1');
        Route::get('/children/{child}/export/zip', [ExportController::class, 'childZip'])->name('export.zip')->middleware('throttle:3,1');

    });

    // Search routes
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// SaaS & Subscription routes — must be registered before catch-all public profile route
require __DIR__.'/saas.php';
require __DIR__.'/subscription.php';

// Tenant Admin routes — must be registered before catch-all public profile route
require __DIR__.'/tenant-admin.php';

// Auth routes must be registered before the catch-all public profile route
require __DIR__.'/auth.php';

// Public profile route — must be after all other routes to avoid conflicts
Route::get('/{slug}', PublicProfileController::class)->name('public.profile');
