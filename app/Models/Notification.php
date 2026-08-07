<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $child_id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property string|null $icon
 * @property string|null $action_url
 * @property bool $is_read
 * @property Carbon|null $read_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Child|null $child
 * @property-read string $type_label
 * @property-read string $type_color
 * @property-read string $formatted_date
 */
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'child_id',
        'title',
        'message',
        'type',
        'icon',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the child related to the notification.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'reminder' => 'Pengingat',
            'info' => 'Informasi',
            'warning' => 'Peringatan',
            'success' => 'Berhasil',
            default => $this->type,
        };
    }

    /**
     * Get the Tailwind color class for the type.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'reminder' => 'bg-lavender/20 text-lavender-700',
            'info' => 'bg-skyBlue/20 text-skyBlue-700',
            'warning' => 'bg-warmYellow/20 text-amber-700',
            'success' => 'bg-mintGreen/20 text-emerald-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get the formatted creation date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
