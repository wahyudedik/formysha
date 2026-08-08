<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string|null $organization_name
 * @property string|null $login_heading
 * @property string|null $login_subheading
 * @property string|null $logo_path
 * @property string|null $favicon_path
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string|null $accent_color
 * @property string|null $footer_text
 * @property string|null $email_sender_name
 * @property string|null $email_sender_email
 * @property bool $is_white_label_enabled
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
        'login_heading',
        'login_subheading',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'footer_text',
        'email_sender_name',
        'email_sender_email',
        'is_white_label_enabled',
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
            'is_white_label_enabled' => 'boolean',
        ];
    }

    /**
     * Get the tenant that owns this branding.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if white label is enabled for this tenant.
     */
    public function isWhiteLabel(): bool
    {
        return $this->is_white_label_enabled;
    }

    /**
     * Get the custom favicon URL if available.
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon_path) {
            return storage_path('app/public/'.$this->favicon_path);
        }

        return null;
    }
}
