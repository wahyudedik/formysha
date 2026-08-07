<?php

namespace App\Models;

use Database\Factories\HealthRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property int $user_id
 * @property string $type
 * @property string $name
 * @property string|null $description
 * @property string $date
 * @property string|null $doctor
 * @property string|null $hospital
 * @property string|null $notes
 * @property string|null $next_date
 */
class HealthRecord extends Model
{
    /** @use HasFactory<HealthRecordFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'type',
        'name',
        'description',
        'date',
        'doctor',
        'hospital',
        'notes',
        'next_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'next_date' => 'date',
        ];
    }

    /**
     * Get the child that owns the health record.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that owns the health record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'immunization' => 'Imunisasi',
            'illness' => 'Penyakit',
            'medication' => 'Obat',
            'allergy' => 'Alergi',
            'checkup' => 'Pemeriksaan',
            'other' => 'Lainnya',
            default => $this->type,
        };
    }

    /**
     * Get the emoji icon based on type.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'immunization' => '💉',
            'illness' => '🤒',
            'medication' => '💊',
            'allergy' => '⚠️',
            'checkup' => '🩺',
            'other' => '📋',
            default => '📋',
        };
    }

    /**
     * Get the badge color class based on type.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'immunization' => 'bg-skyBlue-100 text-skyBlue-700',
            'illness' => 'bg-softPink-100 text-softPink-700',
            'medication' => 'bg-mintGreen-100 text-mintGreen-700',
            'allergy' => 'bg-softOrange-100 text-softOrange-700',
            'checkup' => 'bg-lavender-100 text-lavender-700',
            'other' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get the formatted date in Indonesian format.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->locale('id')->isoFormat('D MMMM YYYY');
    }

    /**
     * Get the formatted next date in Indonesian format.
     */
    public function getFormattedNextDateAttribute(): ?string
    {
        return $this->next_date?->locale('id')->isoFormat('D MMMM YYYY');
    }
}
