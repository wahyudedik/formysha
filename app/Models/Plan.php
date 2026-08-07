<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $price_monthly
 * @property int|null $price_yearly
 * @property int $max_children
 * @property int $max_photos
 * @property int $max_videos
 * @property int $max_storage_mb
 * @property int|null $max_family_members
 * @property int $max_export_per_day
 * @property array|null $features
 * @property bool $is_active
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Plan extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Plan $plan): void {
            if (empty($plan->id)) {
                $plan->id = (string) Str::uuid();
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
        'description',
        'price_monthly',
        'price_yearly',
        'max_children',
        'max_photos',
        'max_videos',
        'max_storage_mb',
        'max_family_members',
        'max_export_per_day',
        'features',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_monthly' => 'integer',
            'price_yearly' => 'integer',
            'max_children' => 'integer',
            'max_photos' => 'integer',
            'max_videos' => 'integer',
            'max_storage_mb' => 'integer',
            'max_family_members' => 'integer',
            'max_export_per_day' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get all subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the formatted monthly price.
     */
    public function getPriceMonthlyFormatted(): string
    {
        if ($this->price_monthly === 0) {
            return 'Gratis';
        }

        return 'Rp '.number_format($this->price_monthly, 0, ',', '.');
    }

    /**
     * Get the formatted yearly price.
     */
    public function getPriceYearlyFormatted(): string
    {
        if ($this->price_yearly === 0 || $this->price_yearly === null) {
            return 'Gratis';
        }

        return 'Rp '.number_format($this->price_yearly, 0, ',', '.');
    }

    /**
     * Get the formatted storage limit.
     */
    public function getStorageFormatted(): string
    {
        if ($this->max_storage_mb === -1) {
            return 'Unlimited';
        }

        if ($this->max_storage_mb >= 1024) {
            return round($this->max_storage_mb / 1024, 1).' GB';
        }

        return $this->max_storage_mb.' MB';
    }
}
