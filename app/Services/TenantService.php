<?php

namespace App\Services;

use App\Enums\TenantType;
use App\Models\Facility;
use App\Models\Plan;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new B2C tenant with owner and default subscription.
     *
     * @param  array{name: string, slug?: string, domain?: string|null}  $data
     */
    public function createTenant(array $data, User $owner): Tenant
    {
        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'domain' => $data['domain'] ?? null,
            'type' => TenantType::Family,
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
     * Create a new B2B tenant (facility) with owner and default subscription.
     *
     * @param  array{
     *     name: string,
     *     type: string,
     *     address?: string,
     *     phone?: string,
     *     email_institution?: string,
     *     website?: string,
     *     license_number?: string,
     *     description?: string,
     *     facility?: array{city?: string, province?: string, postal_code?: string, operating_hours?: array, facilities?: array},
     *     owner?: array{name: string, email: string, password: string},
     * }  $data
     */
    public function createB2BTenant(array $data, ?User $owner = null): Tenant
    {
        $tenantType = TenantType::from($data['type']);

        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'type' => $tenantType,
            'facility_type' => $tenantType->value,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email_institution' => $data['email_institution'] ?? null,
            'website' => $data['website'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        // Create facility record
        if (isset($data['facility'])) {
            Facility::create([
                'tenant_id' => $tenant->id,
                'name' => $data['name'],
                'address' => $data['address'] ?? null,
                'city' => $data['facility']['city'] ?? null,
                'province' => $data['facility']['province'] ?? null,
                'postal_code' => $data['facility']['postal_code'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email_institution'] ?? null,
                'website' => $data['website'] ?? null,
                'operating_hours' => $data['facility']['operating_hours'] ?? null,
                'facilities' => $data['facility']['facilities'] ?? null,
            ]);
        }

        // Assign owner if provided
        if ($owner) {
            $owner->update([
                'tenant_id' => $tenant->id,
                'role' => 'tenant_admin',
            ]);

            // Create staff record for owner as facility_admin
            Staff::create([
                'user_id' => $owner->id,
                'tenant_id' => $tenant->id,
                'staff_role' => 'staff_admin',
                'is_active' => true,
            ]);
        }

        // Create default free subscription
        $freePlan = Plan::where('slug', 'free')->first();

        if ($freePlan) {
            $subscription = new SubscriptionService;
            $subscription->createSubscription($tenant, $freePlan);
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
     * Get current tenant from session or authenticated user's tenant_id.
     */
    public function getCurrentTenant(): ?Tenant
    {
        $tenantId = Session::get('tenant_id');

        if ($tenantId) {
            return Tenant::find($tenantId);
        }

        // Fallback: resolve from authenticated user's tenant_id
        $user = request()->user();
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                // Cache in session for subsequent requests
                $this->switchTenant($tenant);

                return $tenant;
            }
        }

        return null;
    }

    /**
     * Get tenant usage statistics.
     *
     * @return array{children: int, photos: int, videos: int, storage_used: int, storage_limit: int, max_children: int, max_photos: int, max_videos: int, max_storage_mb: int, staff: int, patients: int, clinical_notes: int}
     */
    public function getTenantUsage(Tenant $tenant): array
    {
        $plan = $tenant->activeSubscription?->plan;

        $usage = [
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

        // Add B2B-specific usage if applicable
        if ($tenant->isB2B()) {
            $b2bUsage = $tenant->getB2BUsage();
            $usage['staff'] = $b2bUsage['staff'];
            $usage['patients'] = $b2bUsage['patients'];
            $usage['clinical_notes'] = $b2bUsage['clinical_notes'];
            $usage['max_staff'] = $plan?->getFeatureLimit('max_staff') ?? 0;
            $usage['max_patients'] = $plan?->getFeatureLimit('max_patients') ?? 0;
            $usage['max_clinical_notes'] = $plan?->getFeatureLimit('max_clinical_notes') ?? 0;
        }

        return $usage;
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
            'family_members' => $tenant->canAddFamilyMember(),
            'storage' => $tenant->getStorageUsed() < $tenant->getStorageLimit(),
            // B2B features
            'staff' => $tenant->canAddStaff(),
            'patients' => $tenant->canAddPatientLink(),
            'clinical_notes' => $tenant->canCreateClinicalNote(),
            'referrals' => $tenant->canCreateReferral(),
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

    // ─── B2B-Specific Methods ───────────────────────────────────

    /**
     * Get all staff members for the tenant.
     */
    public function getTenantStaff(Tenant $tenant): Collection
    {
        return $tenant->staff()->with('user')->get();
    }

    /**
     * Get all patient links for the tenant.
     */
    public function getTenantPatientLinks(Tenant $tenant): Collection
    {
        return $tenant->patientLinks()->with(['child', 'user'])->get();
    }

    /**
     * Get staff count for the tenant.
     */
    public function getStaffCount(Tenant $tenant): int
    {
        return $tenant->getStaffCount();
    }

    /**
     * Get patient link count for the tenant.
     */
    public function getPatientLinkCount(Tenant $tenant): int
    {
        return $tenant->getPatientLinkCount();
    }

    /**
     * Get clinical note count for the tenant.
     */
    public function getClinicalNoteCount(Tenant $tenant): int
    {
        return $tenant->getClinicalNoteCount();
    }
}
