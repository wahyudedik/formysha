<?php

namespace App\Models;

use App\Enums\ConsentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Consent record untuk tracking izin orang tua terhadap data anak.
 *
 * @property int $id
 * @property int $user_id
 * @property int $child_id
 * @property ConsentType $consent_type
 * @property bool $granted
 * @property string|null $notes
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $revoked_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Consent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'child_id',
        'consent_type',
        'granted',
        'notes',
        'ip_address',
        'user_agent',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'consent_type' => ConsentType::class,
            'granted' => 'boolean',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke user yang memberikan consent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke child yang datanya diberi consent.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Apakah consent ini aktif (granted dan belum di-revoke)?
     */
    public function isActive(): bool
    {
        return $this->granted && $this->revoked_at === null;
    }

    /**
     * Revoke consent ini.
     */
    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }

    /**
     * Scope: hanya consent yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('granted', true)->whereNull('revoked_at');
    }

    /**
     * Scope: berdasarkan tipe consent.
     */
    public function scopeOfType($query, ConsentType $type)
    {
        return $query->where('consent_type', $type);
    }
}
