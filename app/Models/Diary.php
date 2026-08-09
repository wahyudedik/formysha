<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\DiaryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $child_id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property string|null $mood
 * @property string $diary_date
 * @property string|null $weather
 * @property bool $is_private
 */
class Diary extends Model
{
    /** @use HasFactory<DiaryFactory> */
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
        'content',
        'mood',
        'diary_date',
        'weather',
        'is_private',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'diary_date' => 'date',
        ];
    }

    /**
     * Get the child that owns the diary.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that created the diary.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media associated with the diary.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
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
     * Get the weather label in Indonesian.
     */
    public function getWeatherLabelAttribute(): string
    {
        return match ($this->weather) {
            'sunny' => '☀️ Cerah',
            'cloudy' => '☁️ Berawan',
            'rainy' => '🌧️ Hujan',
            'windy' => '💨 Berangin',
            'snowy' => '❄️ Bersalju',
            default => '-',
        };
    }

    /**
     * Get the formatted diary date.
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->diary_date instanceof Carbon
            ? $this->diary_date
            : Carbon::parse($this->diary_date);

        return $date->locale('id')->isoFormat('D MMMM YYYY');
    }
}
