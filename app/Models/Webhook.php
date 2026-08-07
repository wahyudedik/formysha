<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $url
 * @property string $secret
 * @property array $events
 * @property bool $is_active
 * @property \Carbon\Carbon|null $last_triggered_at
 * @property int $failure_count
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Webhook extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Webhook $webhook): void {
            if (empty($webhook->id)) {
                $webhook->id = (string) Str::uuid();
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
        'url',
        'secret',
        'events',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
            'last_triggered_at' => 'datetime',
            'failure_count' => 'integer',
        ];
    }

    /**
     * Get the tenant that owns the webhook.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the logs for this webhook.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    /**
     * Scope to only include active webhooks.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }
}
