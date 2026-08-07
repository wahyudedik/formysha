<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with owner and default subscription.
     *
     * @param  array{name: string, slug?: string, domain?: string|null}  $data
     */
    public function createTenant(array $data, User $owner): Tenant
    {
        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'domain' => $data['domain'] ?? null,
            'is_active' => true,
        ]);

        // Assign owner as tenant_admin
        $owner->update([
            'tenant_id' => $tenant->id,
            'role' => 'tenant_admin',
        ]);

        // Create default free subscription
        $freePlan = Plan::where('slug', 'free')->first();

        if ($freePlan) {
            $subscription = new SubscriptionService;
            $subscription->createSubscription($tenant, $freePlan);

            // Activate immediately for free plan
            $subscription->activateFreePlan($tenant);
        }

        return $tenant;
    }

    /**
     * Switch tenant context in session.
     */
    public function switchTenant(Tenant $tenant): void
    {
        Session::put('tenant_id', $tenant->id);
        Session::save();
    }

    /**
     * Get current tenant from session or request.
     */
    public function getCurrentTenant(): ?Tenant
    {
        $tenantId = Session::get('tenant_id');

        if ($tenantId) {
            return Tenant::find($tenantId);
        }

        return null;
    }

    /**
     * Get tenant usage statistics.
     *
     * @return array{children: int, photos: int, videos: int, storage_used: int, storage_limit: int, family_members: int}
     */
    public function getTenantUsage(Tenant $tenant): array
    {
        $plan = $tenant->activeSubscription?->plan;

        return [
            'children' => $tenant->getChildCount(),
            'photos' => $tenant->getPhotoCount(),
            'videos' => $tenant->getVideoCount(),
            'storage_used' => $tenant->getStorageUsed(),
            'storage_limit' => $tenant->getStorageLimit(),
            'max_children' => $plan?->max_children ?? 0,
            'max_photos' => $plan?->max_photos ?? 0,
            'max_videos' => $plan?->max_videos ?? 0,
            'max_storage_mb' => $plan?->max_storage_mb ?? 0,
        ];
    }

    /**
     * Check if a tenant is still within a feature's limit.
     */
    public function checkFeatureLimit(Tenant $tenant, string $feature): bool
    {
        return match ($feature) {
            'children' => $tenant->canAddChild(),
            'photos', 'upload_photo' => $tenant->canUploadPhoto(),
            'videos', 'upload_video' => $tenant->canUploadVideo(),
            'storage' => $tenant->getStorageUsed() < $tenant->getStorageLimit(),
            default => true,
        };
    }

    /**
     * Get all children count for the tenant.
     */
    public function getChildCount(Tenant $tenant): int
    {
        return $tenant->getChildCount();
    }

    /**
     * Get photo count for the tenant.
     */
    public function getPhotoCount(Tenant $tenant): int
    {
        return $tenant->getPhotoCount();
    }

    /**
     * Get video count for the tenant.
     */
    public function getVideoCount(Tenant $tenant): int
    {
        return $tenant->getVideoCount();
    }

    /**
     * Get storage used in bytes for the tenant.
     */
    public function getStorageUsed(Tenant $tenant): int
    {
        return $tenant->getStorageUsed();
    }

    /**
     * Get storage limit in bytes for the tenant.
     */
    public function getStorageLimit(Tenant $tenant): int
    {
        return $tenant->getStorageLimit();
    }

    /**
     * Get all users in the tenant.
     */
    public function getTenantUsers(Tenant $tenant): Collection
    {
        return $tenant->users()->get();
    }

    /**
     * Check if the tenant has an active subscription.
     */
    public function isSubscriptionActive(Tenant $tenant): bool
    {
        return $tenant->hasActiveSubscription();
    }

    /**
     * Get all children in the tenant.
     */
    public function getTenantChildren(Tenant $tenant): Collection
    {
        return $tenant->children()->get();
    }
}
