<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string|null $organization_name
 * @property string|null $logo_path
 * @property string|null $favicon_path
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string|null $accent_color
 * @property string|null $custom_css
 * @property string|null $custom_domain
 * @property bool $is_domain_verified
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TenantBranding extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (TenantBranding $branding): void {
            if (empty($branding->id)) {
                $branding->id = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'organization_name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'custom_css',
        'custom_domain',
        'is_domain_verified',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_domain_verified' => 'boolean',
        ];
    }

    /**
     * Get the tenant that owns this branding.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
