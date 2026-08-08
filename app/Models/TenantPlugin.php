<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $plugin_id
 * @property bool $is_enabled
 * @property array|null $settings
 * @property Carbon|null $installed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TenantPlugin extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'tenant_plugins';

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (TenantPlugin $tenantPlugin): void {
            if (empty($tenantPlugin->id)) {
                $tenantPlugin->id = (string) Str::uuid();
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
        'plugin_id',
        'is_enabled',
        'settings',
        'installed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'settings' => 'array',
            'installed_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns this plugin installation.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the plugin being installed.
     */
    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }
}
