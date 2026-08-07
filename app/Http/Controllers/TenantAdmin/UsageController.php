<?php

namespace App\Http\Controllers\TenantAdmin;

use Illuminate\Http\Request;
use Illuminate\View\View;

class UsageController extends Controller
{
    /**
     * Tampilkan halaman usage tenant.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $subscription = $tenant->activeSubscription()->with('plan')->first();
        $plan = $subscription?->plan;

        // Hitung penggunaan saat ini
        $childrenCount = $tenant->getChildCount();
        $photoCount = $tenant->getPhotoCount();
        $videoCount = $tenant->getVideoCount();
        $storageUsed = $tenant->getStorageUsed();

        // Batas dari plan
        $maxChildren = $plan?->max_children ?? 0;
        $maxPhotos = $plan?->max_photos ?? 0;
        $maxVideos = $plan?->max_videos ?? 0;
        $maxStorageMb = $plan?->max_storage_mb ?? 0;
        $maxStorageBytes = $maxStorageMb * 1024 * 1024;

        return view('admin.usage.index', compact(
            'tenant',
            'plan',
            'childrenCount',
            'photoCount',
            'videoCount',
            'storageUsed',
            'maxChildren',
            'maxPhotos',
            'maxVideos',
            'maxStorageMb',
            'maxStorageBytes',
        ));
    }
}
