<?php

namespace App\Enums;

/**
 * Status hubungan antara parent dan pasien di fasilitas.
 */
enum PatientLinkStatus: string
{
    /** Menunggu konfirmasi dari parent. */
    case Pending = 'pending';

    /** Link aktif — parent bisa akses data pasien. */
    case Active = 'active';

    /** Link dicabut oleh fasilitas atau parent. */
    case Revoked = 'revoked';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Active => 'Aktif',
            self::Revoked => 'Dicabut',
        };
    }
}
