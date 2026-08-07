<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\GrowthFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property int $user_id
 * @property string $measured_at
 * @property float|null $weight_kg
 * @property float|null $height_cm
 * @property float|null $head_circumference_cm
 * @property string|null $notes
 */
class Growth extends Model
{
    /** @use HasFactory<GrowthFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'measured_at',
        'weight_kg',
        'height_cm',
        'head_circumference_cm',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'measured_at' => 'date',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:1',
            'head_circumference_cm' => 'decimal:1',
        ];
    }

    /**
     * Get the child that owns the growth record.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that owns the growth record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted weight label.
     */
    public function getWeightLabelAttribute(): ?string
    {
        return $this->weight_kg !== null ? number_format($this->weight_kg, 1, ',', '.').' kg' : null;
    }

    /**
     * Get the formatted height label.
     */
    public function getHeightLabelAttribute(): ?string
    {
        return $this->height_cm !== null ? number_format($this->height_cm, 1, ',', '.').' cm' : null;
    }

    /**
     * Get the formatted head circumference label.
     */
    public function getHeadCircumferenceLabelAttribute(): ?string
    {
        return $this->head_circumference_cm !== null ? number_format($this->head_circumference_cm, 1, ',', '.').' cm' : null;
    }

    /**
     * Get the measured date in Indonesian format.
     */
    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->measured_at)->locale('id')->isoFormat('D MMMM YYYY');
    }
}
