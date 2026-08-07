<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $webhook_id
 * @property string $event
 * @property string $payload
 * @property int|null $response_code
 * @property string|null $response_body
 * @property bool $success
 * @property \Carbon\Carbon|null $delivered_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class WebhookLog extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Boot the model and generate UUID on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (WebhookLog $log): void {
            if (empty($log->id)) {
                $log->id = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'response_code',
        'response_body',
        'success',
        'delivered_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Get the webhook that owns this log.
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
