<?php

namespace App\Http\Controllers\Api\TenantAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Child;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Media;
use App\Models\TenantBranding;
use App\Models\TenantSetting;
use App\Models\Timeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantAdminController extends ApiController
{
    /**
     * Get tenant admin dashboard data.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        // Statistik tenant
        $totalChildren = Child::where('tenant_id', $tenant->id)->count();
        $totalTimelines = Timeline::where('tenant_id', $tenant->id)->count();
        $totalPhotos = Media::where('tenant_id', $tenant->id)->where('file_type', 'photo')->count();
        $totalDocuments = Document::where('tenant_id', $tenant->id)->count();
        $totalDiaries = Diary::where('tenant_id', $tenant->id)->count();

        // Aktivitas terbaru
        $recentTimelines = Timeline::where('tenant_id', $tenant->id)
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        $recentDiaries = Diary::where('tenant_id', $tenant->id)
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        $recentActivity = $recentTimelines->concat($recentDiaries)
            ->sortByDesc('created_at')
            ->take(10);

        // Subscription info
        $subscription = $tenant->activeSubscription()->with('plan')->first();
        $plan = $subscription?->plan;

        $dashboard = [
            'tenant' => $tenant,
            'total_children' => $totalChildren,
            'total_timelines' => $totalTimelines,
            'total_photos' => $totalPhotos,
            'total_documents' => $totalDocuments,
            'total_diaries' => $totalDiaries,
            'recent_activity' => $recentActivity->values(),
            'subscription' => $subscription,
            'plan' => $plan,
        ];

        return $this->successResponse($dashboard, 'Data dashboard berhasil diambil');
    }

    /**
     * Update tenant branding.
     */
    public function updateBranding(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'secondary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'custom_css' => ['nullable', 'string', 'max:10000'],
            'custom_domain' => ['nullable', 'string', 'max:255'],
        ]);

        $branding = TenantBranding::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'organization_name' => $tenant->name,
                'primary_color' => '#7DD3FC',
                'secondary_color' => '#6EE7B7',
            ]
        );

        $branding->update($validated);

        return $this->successResponse($branding->fresh(), 'Branding berhasil diperbarui');
    }

    /**
     * Update tenant settings.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        // Simpan atau update setiap setting
        foreach ($validated as $key => $value) {
            TenantSetting::updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        // Update nama organisasi di branding juga jika ada
        if (! empty($validated['organization_name'])) {
            $tenant->branding()->updateOrCreate(
                ['tenant_id' => $tenant->id],
                ['organization_name' => $validated['organization_name']]
            );
        }

        // Return updated settings
        $settings = TenantSetting::where('tenant_id', $tenant->id)
            ->pluck('value', 'key')
            ->toArray();

        return $this->successResponse($settings, 'Pengaturan berhasil disimpan');
    }

    /**
     * Get tenant usage statistics.
     */
    public function usage(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $subscription = $tenant->activeSubscription()->with('plan')->first();
        $plan = $subscription?->plan;

        $childrenCount = $tenant->getChildCount();
        $photoCount = $tenant->getPhotoCount();
        $videoCount = $tenant->getVideoCount();
        $storageUsed = $tenant->getStorageUsed();

        $maxChildren = $plan?->max_children ?? 0;
        $maxPhotos = $plan?->max_photos ?? 0;
        $maxVideos = $plan?->max_videos ?? 0;
        $maxStorageMb = $plan?->max_storage_mb ?? 0;
        $maxStorageBytes = $maxStorageMb * 1024 * 1024;

        $usage = [
            'plan_name' => $plan?->name ?? '-',
            'children' => [
                'used' => $childrenCount,
                'limit' => $maxChildren,
            ],
            'photos' => [
                'used' => $photoCount,
                'limit' => $maxPhotos,
            ],
            'videos' => [
                'used' => $videoCount,
                'limit' => $maxVideos,
            ],
            'storage' => [
                'used' => $storageUsed,
                'limit' => $maxStorageBytes,
                'used_formatted' => $this->formatBytes($storageUsed),
                'limit_formatted' => $this->formatBytes($maxStorageBytes),
            ],
        ];

        return $this->successResponse($usage, 'Data penggunaan berhasil diambil');
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_073_741_824) {
            return round($bytes / 1_073_741_824, 2).' GB';
        }

        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
