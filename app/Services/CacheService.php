<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantBranding;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache TTL in minutes for different data types.
     */
    public const TTL_BRANDING = 60; // 1 hour

    public const TTL_SUBSCRIPTION = 5; // 5 minutes

    public const TTL_FEATURE_LIMITS = 5; // 5 minutes

    public const TTL_CHILDREN_COUNT = 5; // 5 minutes

    /**
     * Get cached tenant branding, or fetch from DB and cache it.
     */
    public function getTenantBranding(Tenant $tenant): ?TenantBranding
    {
        $cacheKey = "tenant:branding:{$tenant->id}";

        return Cache::tags(["tenant:{$tenant->id}"])->remember($cacheKey, self::TTL_BRANDING, function () use ($tenant) {
            return $tenant->branding;
        });
    }

    /**
     * Invalidate cached branding for a tenant.
     */
    public function invalidateBranding(Tenant $tenant): void
    {
        Cache::tags(["tenant:{$tenant->id}"])->flush();
    }

    /**
     * Get cached active subscription status for a tenant.
     */
    public function isSubscriptionActive(Tenant $tenant): bool
    {
        $cacheKey = "tenant:subscription_active:{$tenant->id}";

        return Cache::remember($cacheKey, self::TTL_SUBSCRIPTION, function () use ($tenant) {
            return $tenant->hasActiveSubscription();
        });
    }

    /**
     * Get cached tenant usage stats.
     *
     * @return array{children: int, photos: int, videos: int, storage_used: int}
     */
    public function getTenantUsage(Tenant $tenant): array
    {
        $cacheKey = "tenant:usage:{$tenant->id}";

        return Cache::tags(["tenant:{$tenant->id}"])->remember($cacheKey, self::TTL_CHILDREN_COUNT, function () use ($tenant) {
            return [
                'children' => $tenant->getChildCount(),
                'photos' => $tenant->getPhotoCount(),
                'videos' => $tenant->getVideoCount(),
                'storage_used' => $tenant->getStorageUsed(),
            ];
        });
    }

    /**
     * Invalidate all caches for a tenant.
     */
    public function invalidateTenant(Tenant $tenant): void
    {
        Cache::tags(["tenant:{$tenant->id}"])->flush();
        Cache::forget("tenant:subscription_active:{$tenant->id}");
    }
}
