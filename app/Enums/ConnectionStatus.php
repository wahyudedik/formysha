<?php

namespace App\Enums;

/**
 * Status hubungan (connection) antara keluarga dan organisasi.
 */
enum ConnectionStatus: string
{
    /** Hubungan aktif, akses penuh sesuai permission. */
    case Active = 'active';

    /** Menunggu persetujuan dari pemilik data (keluarga). */
    case Pending = 'pending';

    /** Direferensikan oleh organisasi lain, menunggu registrasi. */
    case Referred = 'referred';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Pending => 'Menunggu',
            self::Referred => 'Direferensikan',
        };
    }

    /**
     * Warna badge untuk UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Pending => 'yellow',
            self::Referred => 'blue',
        };
    }

    /**
     * Semua opsi status yang tersedia.
     *
     * @return list<self>
     */
    public static function options(): array
    {
        return self::cases();
    }
}
