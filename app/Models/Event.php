<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string $event_date
 * @property string|null $event_time
 * @property string $event_type
 * @property bool $is_recurring
 * @property string|null $recurrence_pattern
 * @property Carbon|null $reminder_at
 */
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'title',
        'description',
        'event_date',
        'event_time',
        'event_type',
        'is_recurring',
        'recurrence_pattern',
        'reminder_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_recurring' => 'boolean',
            'reminder_at' => 'datetime',
        ];
    }

    /**
     * Get the child that owns the event.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that created the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event type label in Indonesian.
     */
    public function getEventTypeLabelAttribute(): string
    {
        return match ($this->event_type) {
            'birthday' => '🎂 Ulang Tahun',
            'immunization' => '💉 Imunisasi',
            'appointment' => '🩺 Janji Temu',
            'school' => '🏫 Sekolah',
            'other' => '📌 Lainnya',
            default => $this->event_type,
        };
    }

    /**
     * Get formatted event date.
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->event_date instanceof Carbon
            ? $this->event_date
            : Carbon::parse($this->event_date);

        return $date->locale('id')->isoFormat('D MMMM YYYY');
    }

    /**
     * Get formatted event time.
     */
    public function getFormattedTimeAttribute(): ?string
    {
        if (! $this->event_time) {
            return null;
        }

        $parts = explode(':', $this->event_time);
        $hour = (int) $parts[0];
        $minute = $parts[1] ?? '00';
        $period = $hour >= 12 ? 'WIB' : 'WIB';

        return sprintf('%d:%s %s', $hour > 12 ? $hour - 12 : $hour, $minute, $period);
    }

    /**
     * Check if the event is upcoming.
     */
    public function getIsUpcomingAttribute(): bool
    {
        $date = $this->event_date instanceof Carbon
            ? $this->event_date
            : Carbon::parse($this->event_date);

        return $date->isFuture();
    }
}
