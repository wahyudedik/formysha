<?php

namespace App\Models;

use App\Enums\TenantType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property TenantType $type
 * @property string|null $facility_type
 * @property string|null $domain
 * @property string|null $logo
 * @property string|null $custom_domain
 * @property Carbon|null $domain_verified_at
 * @property bool $domain_dns_verified
 * @property bool $is_active
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email_institution
 * @property string|null $website
 * @property string|null $license_number
 * @property string|null $description
 * @property array|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Tenant extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant): void {
            if (empty($tenant->id)) {
                $tenant->id = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'type',
        'facility_type',
        'domain',
        'logo',
        'custom_domain',
        'domain_verified_at',
        'domain_dns_verified',
        'is_active',
        'address',
        'phone',
        'email_institution',
        'website',
        'license_number',
        'description',
        'settings',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TenantType::class,
            'is_active' => 'boolean',
            'domain_dns_verified' => 'boolean',
            'domain_verified_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Get the users belonging to this tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the children belonging to this tenant.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }

    /**
     * Get all family members belonging to this tenant's children.
     */
    public function familyMembers(): HasManyThrough
    {
        return $this->hasManyThrough(FamilyMember::class, Child::class);
    }

    /**
     * Get all subscriptions for this tenant.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for this tenant.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    /**
     * Get the current plan through the active subscription.
     */
    public function currentPlan(): HasManyThrough
    {
        return $this->hasManyThrough(Plan::class, Subscription::class);
    }

    /**
     * Get all payments for this tenant.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all media files owned by this tenant's children (polymorphic).
     *
     * Covers media attached directly to a child as well as media attached
     * to the child's timelines, albums, and diaries, so feature-limit
     * counting cannot be bypassed through nested uploads.
     */
    public function media()
    {
        $childIds = $this->children()->pluck('children.id');

        $timelineIds = Timeline::whereIn('child_id', $childIds)->pluck('id');
        $albumIds = Album::whereIn('child_id', $childIds)->pluck('id');
        $diaryIds = Diary::whereIn('child_id', $childIds)->pluck('id');

        return Media::where(function ($query) use ($childIds, $timelineIds, $albumIds, $diaryIds) {
            $query->where(function ($q) use ($childIds) {
                $q->where('mediable_type', (new Child)->getMorphClass())
                    ->whereIn('mediable_id', $childIds);
            })->orWhere(function ($q) use ($timelineIds) {
                $q->where('mediable_type', (new Timeline)->getMorphClass())
                    ->whereIn('mediable_id', $timelineIds);
            })->orWhere(function ($q) use ($albumIds) {
                $q->where('mediable_type', (new Album)->getMorphClass())
                    ->whereIn('mediable_id', $albumIds);
            })->orWhere(function ($q) use ($diaryIds) {
                $q->where('mediable_type', (new Diary)->getMorphClass())
                    ->whereIn('mediable_id', $diaryIds);
            });
        });
    }

    /**
     * Get the settings for this tenant.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    /**
     * Get the branding for this tenant.
     */
    public function branding(): HasOne
    {
        return $this->hasOne(TenantBranding::class);
    }

    /**
     * Get the audit logs for this tenant.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the installed plugins for this tenant.
     */
    public function tenantPlugins(): HasMany
    {
        return $this->hasMany(TenantPlugin::class);
    }

    /**
     * Get the facility details for B2B tenants.
     */
    public function facility(): HasOne
    {
        return $this->hasOne(Facility::class);
    }

    /**
     * Get all staff members for this tenant.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    /**
     * Get all patient links for this tenant.
     */
    public function patientLinks(): HasMany
    {
        return $this->hasMany(PatientLink::class, 'facility_tenant_id');
    }

    /**
     * Get all clinical notes for this tenant.
     */
    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    /**
     * Get all referrals originating from this tenant.
     */
    public function referralsFrom(): HasMany
    {
        return $this->hasMany(Referral::class, 'from_tenant_id');
    }

    /**
     * Get all referrals received by this tenant.
     */
    public function referralsTo(): HasMany
    {
        return $this->hasMany(Referral::class, 'to_tenant_id');
    }

    /**
     * Get the connections for this tenant.
     */
    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    // ─── Type Helpers ────────────────────────────────────────────

    /**
     * Check if this is a B2B tenant (hospital, clinic, etc.).
     */
    public function isB2B(): bool
    {
        if (! $this->type) {
            return false;
        }

        /** @var TenantType $type */
        $type = $this->type;

        return $type->isB2B();
    }

    /**
     * Check if this is a B2C tenant (family).
     */
    public function isB2C(): bool
    {
        if (! $this->type) {
            return true;
        }

        /** @var TenantType $type */
        $type = $this->type;

        return $type->isB2C();
    }

    /**
     * Get the facility type label.
     */
    public function getFacilityTypeLabel(): ?string
    {
        if (! $this->facility_type) {
            return null;
        }

        return TenantType::tryFrom($this->facility_type)?->label();
    }

    /**
     * Get the type label.
     */
    public function getTypeLabel(): string
    {
        if (! $this->type) {
            return 'Keluarga';
        }

        /** @var TenantType $type */
        $type = $this->type;

        return $type->label();
    }

    // ─── Status Checks ──────────────────────────────────────────

    /**
     * Check if the tenant is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the tenant has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    // ─── Feature Limits (B2C) ───────────────────────────────────

    /**
     * Check if the tenant can add another child.
     */
    public function canAddChild(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        if ($plan->max_children === -1) {
            return true;
        }

        return $this->children()->count() < $plan->max_children;
    }

    /**
     * Check if the tenant can upload a photo.
     */
    public function canUploadPhoto(): bool
    {
        return $this->canUploadAdditionalPhotos(1);
    }

    /**
     * Check if the tenant can upload N more photos.
     *
     * A limit of -1 or null means unlimited.
     */
    public function canUploadAdditionalPhotos(int $count = 1): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        $maxPhotos = $plan->max_photos;

        if ($maxPhotos === null || $maxPhotos === -1) {
            return true;
        }

        return $this->getPhotoCount() + $count <= (int) $maxPhotos;
    }

    /**
     * Check if the tenant can add a family member.
     */
    public function canAddFamilyMember(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        if ($plan->max_family_members === -1) {
            return true;
        }

        return $this->familyMembers()->count() < $plan->max_family_members;
    }

    /**
     * Check if the tenant can upload a video.
     */
    public function canUploadVideo(): bool
    {
        return $this->canUploadAdditionalVideos(1);
    }

    /**
     * Check if the tenant can upload N more videos.
     *
     * A limit of -1 or null means unlimited.
     */
    public function canUploadAdditionalVideos(int $count = 1): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        $maxVideos = $plan->max_videos;

        if ($maxVideos === null || $maxVideos === -1) {
            return true;
        }

        return $this->getVideoCount() + $count <= (int) $maxVideos;
    }

    /**
     * Check if the tenant can store N more bytes of media.
     *
     * A limit of -1 or null means unlimited.
     */
    public function canUploadStorage(int $additionalBytes = 0): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        $maxStorageMb = $plan->max_storage_mb;

        if ($maxStorageMb === null || $maxStorageMb === -1) {
            return true;
        }

        $limitBytes = (int) $maxStorageMb * 1024 * 1024;

        return $this->getStorageUsed() + $additionalBytes <= $limitBytes;
    }

    // ─── B2B Feature Limits ─────────────────────────────────────

    /**
     * Check if the tenant can add another staff member.
     *
     * A missing or -1 limit in the plan features means unlimited.
     */
    public function canAddStaff(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        $maxStaff = $plan->getFeatureLimit('max_staff');

        if ($maxStaff === null || $maxStaff === -1) {
            return true;
        }

        return $this->staff()->count() < (int) $maxStaff;
    }

    /**
     * Check if the tenant can add another patient link.
     *
     * A missing or -1 limit in the plan features means unlimited.
     */
    public function canAddPatientLink(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        $maxPatients = $plan->getFeatureLimit('max_patients');

        if ($maxPatients === null || $maxPatients === -1) {
            return true;
        }

        return $this->patientLinks()->count() < (int) $maxPatients;
    }

    /**
     * Check if the tenant can create a clinical note.
     *
     * A missing or -1 limit in the plan features means unlimited.
     */
    public function canCreateClinicalNote(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        $maxNotes = $plan->getFeatureLimit('max_clinical_notes');

        if ($maxNotes === null || $maxNotes === -1) {
            return true;
        }

        return $this->clinicalNotes()->count() < (int) $maxNotes;
    }

    /**
     * Check if the tenant can create a referral.
     */
    public function canCreateReferral(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        /** @var bool $enabled */
        $enabled = $plan->getFeatureLimit('referrals_enabled');

        return (bool) $enabled;
    }

    // ─── Usage Stats ────────────────────────────────────────────

    /**
     * Get the total storage used by this tenant in bytes.
     */
    public function getStorageUsed(): int
    {
        return $this->media()->sum('file_size') ?? 0;
    }

    /**
     * Get the storage limit for this tenant in bytes.
     */
    public function getStorageLimit(): int
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan || $plan->max_storage_mb === -1) {
            return PHP_INT_MAX;
        }

        return $plan->max_storage_mb * 1024 * 1024;
    }

    /**
     * Get the number of children in this tenant.
     */
    public function getChildCount(): int
    {
        return $this->children()->count();
    }

    /**
     * Get the number of photos in this tenant.
     */
    public function getPhotoCount(): int
    {
        return $this->media()->where('file_type', 'photo')->count();
    }

    /**
     * Get the number of videos in this tenant.
     */
    public function getVideoCount(): int
    {
        return $this->media()->where('file_type', 'video')->count();
    }

    /**
     * Get the number of staff members in this tenant.
     */
    public function getStaffCount(): int
    {
        return $this->staff()->count();
    }

    /**
     * Get the number of patient links in this tenant.
     */
    public function getPatientLinkCount(): int
    {
        return $this->patientLinks()->count();
    }

    /**
     * Get the number of clinical notes in this tenant.
     */
    public function getClinicalNoteCount(): int
    {
        return $this->clinicalNotes()->count();
    }

    // ─── B2B Usage for TenantService ────────────────────────────

    /**
     * Get B2B-specific usage statistics.
     *
     * @return array{staff: int, patients: int, clinical_notes: int}
     */
    public function getB2BUsage(): array
    {
        return [
            'staff' => $this->getStaffCount(),
            'patients' => $this->getPatientLinkCount(),
            'clinical_notes' => $this->getClinicalNoteCount(),
        ];
    }
}
