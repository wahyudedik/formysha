<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $child_id
 * @property int $user_id
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property string $file_path
 * @property string $file_name
 * @property int $file_size
 * @property string|null $issued_date
 * @property string|null $expiry_date
 * @property bool $is_private
 */
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'child_id',
        'user_id',
        'name',
        'type',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'issued_date',
        'expiry_date',
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
            'file_size' => 'integer',
            'issued_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    /**
     * Get the child that owns the document.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the user that uploaded the document.
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
            'birth_certificate' => '📜 Akta Lahir',
            'family_card' => '🏠 Kartu Keluarga',
            'kia' => '🪪 KIA',
            'bpjs' => '🏥 BPJS',
            'passport' => '✈️ Paspor',
            'certificate' => '🎓 Sertifikat',
            'report_card' => '📋 Rapor',
            'other' => '📄 Lainnya',
            default => $this->type,
        };
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }

    /**
     * Get formatted issued date.
     */
    public function getFormattedIssuedDateAttribute(): ?string
    {
        if (! $this->issued_date) {
            return null;
        }

        $date = $this->issued_date instanceof Carbon
            ? $this->issued_date
            : Carbon::parse($this->issued_date);

        return $date->locale('id')->isoFormat('D MMMM YYYY');
    }

    /**
     * Get formatted expiry date.
     */
    public function getFormattedExpiryDateAttribute(): ?string
    {
        if (! $this->expiry_date) {
            return null;
        }

        $date = $this->expiry_date instanceof Carbon
            ? $this->expiry_date
            : Carbon::parse($this->expiry_date);

        return $date->locale('id')->isoFormat('D MMMM YYYY');
    }

    /**
     * Check if the document has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (! $this->expiry_date) {
            return false;
        }

        $date = $this->expiry_date instanceof Carbon
            ? $this->expiry_date
            : Carbon::parse($this->expiry_date);

        return $date->isPast();
    }
}
