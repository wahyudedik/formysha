<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ChildFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $tenant_id
 * @property string $name
 * @property string $slug
 * @property string|null $nickname
 * @property string $gender
 * @property string $date_of_birth
 * @property string|null $place_of_birth
 * @property string|null $blood_type
 * @property string|null $photo
 * @property string|null $bio
 * @property bool $is_public
 * @property array|null $public_profile_data
 */
class Child extends Model
{
    /** @use HasFactory<ChildFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'tenant_id',
        'name',
        'slug',
        'nickname',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'blood_type',
        'photo',
        'bio',
        'is_public',
        'public_profile_data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_public' => 'boolean',
            'public_profile_data' => 'array',
        ];
    }

    /**
     * Automatically generate slug from name before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Child $child): void {
            if (empty($child->slug)) {
                $child->slug = Str::slug($child->name);
            }
        });

        static::updating(function (Child $child): void {
            if ($child->isDirty('name') && ! $child->isDirty('slug')) {
                $child->slug = Str::slug($child->name);
            }
        });
    }

    /**
     * Get the route key for model binding.
     *
     * Uses slug instead of numeric ID for SEO-friendly URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the user (parent/guardian) that owns the child.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tenant that owns this child.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the family members for the child.
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    /**
     * Get the timeline entries for the child.
     */
    public function timelines(): HasMany
    {
        return $this->hasMany(Timeline::class);
    }

    /**
     * Get all media attached to this child (polymorphic).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the albums for the child.
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Get the diary entries for the child.
     */
    public function diaries(): HasMany
    {
        return $this->hasMany(Diary::class);
    }

    /**
     * Get the documents for the child.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the health records for the child.
     */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    /**
     * Get the events for the child.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the growth records for the child.
     */
    public function growths(): HasMany
    {
        return $this->hasMany(Growth::class);
    }

    /**
     * Get the child's achievements.
     */
    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    /**
     * Get the child's age in years.
     */
    public function getAgeAttribute(): ?string
    {
        if (! $this->date_of_birth) {
            return null;
        }

        $birth = $this->date_of_birth instanceof Carbon
            ? $this->date_of_birth
            : Carbon::parse($this->date_of_birth);

        $now = now();
        $years = (int) $birth->diffInYears($now);
        $months = (int) $birth->copy()->addYears($years)->diffInMonths($now);

        if ($years === 0) {
            return "{$months} bulan";
        }

        return "{$years} tahun {$months} bulan";
    }

    /**
     * Get the child's public profile URL slug.
     */
    public function getPublicUrlAttribute(): string
    {
        return '/'.$this->slug;
    }
}
