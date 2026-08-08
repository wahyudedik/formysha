<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $version
 * @property string $author
 * @property string|null $author_url
 * @property string|null $icon
 * @property array|null $hooks
 * @property array|null $routes
 * @property array|null $permissions
 * @property bool $is_active
 * @property bool $is_official
 * @property int $install_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Plugin extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Plugin $plugin): void {
            if (empty($plugin->id)) {
                $plugin->id = (string) Str::uuid();
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
        'version',
        'author',
        'author_url',
        'icon',
        'hooks',
        'routes',
        'permissions',
        'is_active',
        'is_official',
        'install_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hooks' => 'array',
            'routes' => 'array',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'is_official' => 'boolean',
            'install_count' => 'integer',
        ];
    }

    /**
     * Get the tenant plugins for this plugin.
     */
    public function tenantPlugins(): HasMany
    {
        return $this->hasMany(TenantPlugin::class);
    }

    /**
     * Get the plugin logs for this plugin.
     */
    public function pluginLogs(): HasMany
    {
        return $this->hasMany(PluginLog::class);
    }

    /**
     * Scope: active plugins only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: official plugins only.
     */
    public function scopeOfficial($query)
    {
        return $query->where('is_official', true);
    }
}
