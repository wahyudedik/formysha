<?php

namespace App\Enums;

/**
 * Status rujukan antar fasilitas kesehatan.
 */
enum ReferralStatus: string
{
    /** Rujukan baru, menunggu diterima. */
    case Pending = 'pending';

    /** Rujukan diterima oleh fasilitas tujuan. */
    case Accepted = 'accepted';

    /** Rujukan selesai ditangani. */
    case Completed = 'completed';

    /** Rujukan dibatalkan. */
    case Cancelled = 'cancelled';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Accepted => 'Diterima',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Warna badge untuk UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Accepted => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }
}
