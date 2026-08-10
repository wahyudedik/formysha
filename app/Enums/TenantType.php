<?php

namespace App\Enums;

/**
 * Tipe tenant menentukan apakah tenant adalah keluarga (B2C) atau institusi (B2B).
 */
enum TenantType: string
{
    /** Keluarga atau individu — mode B2C default. */
    case Family = 'family';

    /** Rumah sakit — mode B2B. */
    case Hospital = 'hospital';

    /** Klinik — mode B2B. */
    case Clinic = 'clinic';

    /** Praktik bidan — mode B2B. */
    case Midwifery = 'midwifery';

    /** Posyandu — mode B2B. */
    case Posyandu = 'posyandu';

    /** Daycare — mode B2B. */
    case Daycare = 'daycare';

    /** PAUD / TK / Sekolah — mode B2B. */
    case School = 'school';

    /**
     * Apakah tipe ini adalah B2B (institusi)?
     */
    public function isB2B(): bool
    {
        return $this !== self::Family;
    }

    /**
     * Apakah tipe ini adalah B2C (keluarga)?
     */
    public function isB2C(): bool
    {
        return $this === self::Family;
    }

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Family => 'Keluarga',
            self::Hospital => 'Rumah Sakit',
            self::Clinic => 'Klinik',
            self::Midwifery => 'Praktik Bidan',
            self::Posyandu => 'Posyandu',
            self::Daycare => 'Daycare',
            self::School => 'Sekolah',
        };
    }

    /**
     * Deskripsi singkat untuk UI.
     */
    public function description(): string
    {
        return match ($this) {
            self::Family => 'Untuk keluarga dan individu yang ingin mendokumentasikan perjalanan hidup anak.',
            self::Hospital => 'Rumah sakit dengan layanan kesehatan anak dan ibu.',
            self::Clinic => 'Klinik kesehatan dengan layanan rawat jalan.',
            self::Midwifery => 'Praktik bidan yang melayani persalinan dan kesehatan ibu.',
            self::Posyandu => 'Pos Pelayanan Terpadu untuk kesehatan ibu dan anak.',
            self::Daycare => 'Tempat penitipan anak dengan layanan perawatan harian.',
            self::School => 'Lembaga pendidikan anak usia dini (PAUD/TK/SD).',
        };
    }

    /**
     * Semua tipe B2B yang tersedia.
     *
     * @return list<self>
     */
    public static function b2bTypes(): array
    {
        return array_filter(self::cases(), fn (self $type) => $type->isB2B());
    }
}
