<?php

namespace App\Models;

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
 * @property string|null $domain
 * @property string|null $logo
 * @property bool $is_active
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
        'name',
        'slug',
        'domain',
        'logo',
        'is_active',
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
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

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
     * Get all media files through children.
     */
    /**
     * Get all media files through children (polymorphic relationship).
     */
    public function media()
    {
        $childIds = $this->children()->pluck('children.id');

        return Media::where('mediable_type', (new Child)->getMorphClass())
            ->whereIn('mediable_id', $childIds);
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
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        if ($plan->max_photos === -1) {
            return true;
        }

        return $this->getPhotoCount() < $plan->max_photos;
    }

    /**
     * Check if the tenant can upload a video.
     */
    public function canUploadVideo(): bool
    {
        $plan = $this->activeSubscription?->plan;

        if (! $plan) {
            return false;
        }

        if ($plan->max_videos === -1) {
            return true;
        }

        return $this->getVideoCount() < $plan->max_videos;
    }

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
}
