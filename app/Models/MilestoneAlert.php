<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\MilestoneAlertFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $child_id
 * @property string $type
 * @property string $title
 * @property string|null $description
 * @property string|null $icon
 * @property string $alert_date
 * @property string $milestone_date
 * @property bool $is_dismissed
 * @property Carbon|null $dismissed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Child $child
 * @property-read string $days_until_label
 */
class MilestoneAlert extends Model
{
    /** @use HasFactory<MilestoneAlertFactory> */
    use HasFactory;

    public const TYPE_BIRTHDAY = 'birthday';

    public const TYPE_MONTHLY_AGE = 'monthly_age';

    public const TYPE_GROWTH_RECORD = 'growth_record';

    public const TYPE_IMMUNIZATION = 'immunization';

    public const TYPE_YEARLY_AGE = 'yearly_age';

    /** @var array<string, string> */
    public const TYPES = [
        self::TYPE_BIRTHDAY => [
            'name' => 'Ulang Tahun',
            'icon' => '🎂',
        ],
        self::TYPE_MONTHLY_AGE => [
            'name' => 'Usia Bulanan',
            'icon' => '📅',
        ],
        self::TYPE_YEARLY_AGE => [
            'name' => 'Usia Tahunan',
            'icon' => '🎉',
        ],
        self::TYPE_GROWTH_RECORD => [
            'name' => 'Pencatatan Pertumbuhan',
            'icon' => '📏',
        ],
        self::TYPE_IMMUNIZATION => [
            'name' => 'Jadwal Imunisasi',
            'icon' => '💉',
        ],
    ];

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'child_id',
        'type',
        'title',
        'description',
        'icon',
        'alert_date',
        'milestone_date',
        'is_dismissed',
        'dismissed_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'alert_date' => 'date',
            'milestone_date' => 'date',
            'is_dismissed' => 'boolean',
            'dismissed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get a human-readable label for days until milestone.
     */
    public function getDaysUntilLabelAttribute(): string
    {
        $days = (int) Carbon::today()->diffInDays($this->milestone_date, absolute: false);

        if ($days === 0) {
            return 'Hari ini!';
        }

        if ($days < 0) {
            return abs($days).' hari yang lalu';
        }

        return "Dalam {$days} hari";
    }

    /**
     * Scope: only active (not dismissed) alerts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false);
    }

    /**
     * Scope: alerts due today or in the future.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('alert_date', '<=', now()->toDateString());
    }

    /**
     * Scope: alerts of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Dismiss this alert.
     */
    public function dismiss(): bool
    {
        return $this->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
        ]);
    }
}
