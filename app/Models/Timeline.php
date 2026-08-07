<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\TimelineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $child_id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string $event_date
 * @property string|null $event_time
 * @property string|null $location
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $mood
 * @property array|null $tags
 * @property bool $is_featured
 */
class Timeline extends Model
{
    /** @use HasFactory<TimelineFactory> */
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
        'location',
        'latitude',
        'longitude',
        'mood',
        'tags',
        'is_featured',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get the child that owns the timeline.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user (parent/guardian) that created the timeline.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media associated with the timeline.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'mediable_id')
            ->where('mediable_type', static::class);
    }

    /**
     * Get the mood label in Indonesian.
     */
    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood) {
            'happy' => '😊 Bahagia',
            'excited' => '🤩 Antusias',
            'calm' => '😌 Tenang',
            'sad' => '😢 Sedih',
            'surprised' => '😲 Terkejut',
            'loved' => '🥰 Disayang',
            default => '-',
        };
    }

    /**
     * Get the formatted event date.
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->event_date instanceof Carbon
            ? $this->event_date
            : Carbon::parse($this->event_date);

        return $date->locale('id')->isoFormat('D MMMM YYYY');
    }
}
