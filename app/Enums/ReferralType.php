<?php

namespace App\Enums;

/**
 * Tipe rujukan antar fasilitas atau dari fasilitas ke keluarga.
 */
enum ReferralType: string
{
    /** Rujukan dari satu fasilitas kesehatan ke fasilitas lainnya. */
    case FacilityToFacility = 'facility_to_facility';

    /** Rujukan dari fasilitas kesehatan ke keluarga/pasien. */
    case FacilityToFamily = 'facility_to_family';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::FacilityToFacility => 'Fasilitas ke Fasilitas',
            self::FacilityToFamily => 'Fasilitas ke Keluarga',
        };
    }

    /**
     * Deskripsi singkat untuk UI.
     */
    public function description(): string
    {
        return match ($this) {
            self::FacilityToFacility => 'Rujukan dari satu fasilitas kesehatan ke fasilitas lainnya.',
            self::FacilityToFamily => 'Rujukan dari fasilitas kesehatan langsung ke keluarga/pasien.',
        };
    }

    /**
     * Semua opsi tipe rujukan yang tersedia.
     *
     * @return list<self>
     */
    public static function options(): array
    {
        return self::cases();
    }
}
