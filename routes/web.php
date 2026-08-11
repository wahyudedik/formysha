<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ErasureController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\FamilyTreeController;
use App\Http\Controllers\GrowthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MilestoneController;
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

// Language Switcher
Route::post('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Children routes (CRUD without {child} parameter)
    // Store route has feature.limit:children middleware to enforce plan limits
    Route::get('children', [ChildController::class, 'index'])->name('children.index');
    Route::get('children/create', [ChildController::class, 'create'])->name('children.create');
    Route::post('children', [ChildController::class, 'store'])->name('children.store')->middleware('feature.limit:children');

    // All routes with {child} parameter — protected by child.ownership middleware
    Route::middleware('child.ownership')->group(function () {

        // Child show/edit/update/destroy (explicit routes for proper URL generation)
        Route::get('/children/{child}', [ChildController::class, 'show'])->name('children.show');
        Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
        Route::put('children/{child}', [ChildController::class, 'update'])->name('children.update');
        Route::delete('children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');

        // Family member routes (nested under children)
        Route::get('/children/{child}/family', [FamilyMemberController::class, 'index'])->name('family.index');
        Route::get('/children/{child}/family/create', [FamilyMemberController::class, 'create'])->name('family.create');
        Route::post('/children/{child}/family', [FamilyMemberController::class, 'store'])->name('family.store')->middleware('feature.limit:family_members');
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
        Route::get('/children/{child}/growth/{growth}', [GrowthController::class, 'show'])->name('growth.show');
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

        // Achievement routes (nested under children)
        Route::get('/children/{child}/achievements', [AchievementController::class, 'index'])->name('achievements.index');
        Route::post('/children/{child}/achievements/check', [AchievementController::class, 'check'])->name('achievements.check');

        // Milestone routes (nested under children)
        Route::get('/children/{child}/milestones', [MilestoneController::class, 'index'])->name('milestones.index');
        Route::post('/children/{child}/milestones/check', [MilestoneController::class, 'check'])->name('milestones.check');
        Route::post('/children/{child}/milestones/{milestoneAlert}/dismiss', [MilestoneController::class, 'dismiss'])->name('milestones.dismiss');

        // Consent routes (nested under children)
        Route::get('/children/{child}/consent', [ConsentController::class, 'index'])->name('consent.index');
        Route::post('/children/{child}/consent', [ConsentController::class, 'update'])->name('consent.update');

        // Export routes (nested under children) — throttled to prevent abuse
        Route::get('/children/{child}/export/profile', [ExportController::class, 'childProfile'])->name('export.profile')->middleware('throttle:5,1');
        Route::get('/children/{child}/export/health', [ExportController::class, 'healthRecords'])->name('export.health')->middleware('throttle:5,1');
        Route::get('/children/{child}/export/growth', [ExportController::class, 'growthRecords'])->name('export.growth')->middleware('throttle:5,1');
        Route::get('/children/{child}/export/zip', [ExportController::class, 'childZip'])->name('export.zip')->middleware('throttle:3,1');

        // Media routes (nested under children)
        // Photo/video upload routes have feature.limit middleware to enforce plan limits
        Route::post('/children/{child}/timeline/{timeline}/media', [MediaController::class, 'storeForTimeline'])->name('media.store.timeline')->middleware('feature.limit:photos');
        Route::post('/children/{child}/albums/{album}/media', [MediaController::class, 'storeForAlbum'])->name('media.store.album')->middleware('feature.limit:photos');
        Route::post('/children/{child}/diaries/{diary}/media', [MediaController::class, 'storeForDiary'])->name('media.store.diary')->middleware('feature.limit:photos');
        Route::delete('/children/{child}/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Connection routes (nested under children)
        Route::get('/children/{child}/connections', [ConnectionController::class, 'index'])->name('connections.index');
        Route::get('/children/{child}/connections/create', [ConnectionController::class, 'create'])->name('connections.create');
        Route::post('/children/{child}/connections', [ConnectionController::class, 'store'])->name('connections.store');
        Route::get('/children/{child}/connections/{connection}', [ConnectionController::class, 'show'])->name('connections.show');
        Route::get('/children/{child}/connections/{connection}/edit', [ConnectionController::class, 'edit'])->name('connections.edit');
        Route::put('/children/{child}/connections/{connection}', [ConnectionController::class, 'update'])->name('connections.update');
        Route::delete('/children/{child}/connections/{connection}', [ConnectionController::class, 'destroy'])->name('connections.destroy');
        Route::post('/children/{child}/connections/{connection}/approve', [ConnectionController::class, 'approve'])->name('connections.approve');
        Route::post('/children/{child}/connections/{connection}/reject', [ConnectionController::class, 'reject'])->name('connections.reject');
        Route::post('/children/{child}/connections/{connection}/revoke', [ConnectionController::class, 'revoke'])->name('connections.revoke');

        // Family Tree routes (nested under children)
        Route::get('/children/{child}/family-tree', [FamilyTreeController::class, 'index'])->name('children.family-tree');

        // Erasure routes (nested under children) — child deletion
        Route::delete('/children/{child}/erasure', [ErasureController::class, 'destroyChild'])->name('erasure.destroyChild');

    });

    // Erasure routes — account-level
    Route::get('/erasure', [ErasureController::class, 'index'])->name('erasure.index');
    Route::delete('/erasure/account', [ErasureController::class, 'destroyAccount'])->name('erasure.destroyAccount');

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

// Facility Admin routes — must be registered before catch-all public profile route
require __DIR__.'/facility-admin.php';

// Auth routes must be registered before the catch-all public profile route
require __DIR__.'/auth.php';

// Public profile route — must be after all other routes to avoid conflicts
// Throttled to 60 requests per minute per IP to prevent scraping
Route::get('/{slug}', PublicProfileController::class)
    ->middleware('throttle:60,1')
    ->name('public.profile');
