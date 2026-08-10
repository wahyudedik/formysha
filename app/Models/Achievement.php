<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'child_id',
        'type',
        'name',
        'description',
        'icon',
        'earned_at',
    ];

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
        ];
    }

    /**
     * Achievement types with their metadata.
     */
    public const TYPES = [
        'first_upload' => [
            'name' => 'Foto Pertama',
            'description' => 'Unggah foto pertama untuk buah hati',
            'icon' => '📷',
        ],
        'first_timeline' => [
            'name' => 'Cerita Pertama',
            'description' => 'Buat kenangan timeline pertama',
            'icon' => '📖',
        ],
        'first_diary' => [
            'name' => 'Diari Pertama',
            'description' => 'Tulis catatan harian pertama',
            'icon' => '✍️',
        ],
        'ten_photos' => [
            'name' => 'Kolektor Foto',
            'description' => 'Kumpulkan 10 foto',
            'icon' => '📸',
        ],
        'fifty_photos' => [
            'name' => 'Fotografer',
            'description' => 'Kumpulkan 50 foto',
            'icon' => '🎞️',
        ],
        'hundred_photos' => [
            'name' => 'Master Fotografer',
            'description' => 'Kumpulkan 100 foto',
            'icon' => '🏆',
        ],
        'health_tracker' => [
            'name' => 'Petugas Kesehatan',
            'description' => 'Catat 5 record kesehatan',
            'icon' => '💊',
        ],
        'growth_tracker' => [
            'name' => 'Pemantau Pertumbuhan',
            'description' => 'Catat 10 data pertumbuhan',
            'icon' => '📏',
        ],
        'family_builder' => [
            'name' => 'Pembina Keluarga',
            'description' => 'Tambahkan 3 anggota keluarga',
            'icon' => '👨‍👩‍👧‍👦',
        ],
        'document_keeper' => [
            'name' => 'Penjaga Dokumen',
            'description' => 'Unggah 5 dokumen penting',
            'icon' => '📋',
        ],
        'one_year_streak' => [
            'name' => 'Setia Setahun',
            'description' => 'Aktif mencatat selama 1 tahun',
            'icon' => '🎂',
        ],
    ];

    /**
     * Scope: filter by type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: earned achievements only.
     */
    public function scopeEarned(Builder $query): Builder
    {
        return $query->whereNotNull('earned_at');
    }

    /**
     * Scope: pending achievements only.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('earned_at');
    }

    /**
     * Check if this achievement is earned.
     */
    public function isEarned(): bool
    {
        return $this->earned_at !== null;
    }

    /**
     * Get the user who owns this achievement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the child this achievement belongs to.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
