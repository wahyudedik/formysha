<?php

namespace App\Enums;

/**
 * Tipe consent yang dapat diberikan oleh orang tua untuk data anak.
 */
enum ConsentType: string
{
    /** Persetujuan pengumpulan data umum anak. */
    case DataCollection = 'data_collection';

    /** Persetujuan berbagi foto anak. */
    case PhotoSharing = 'photo_sharing';

    /** Persetujuan akses catatan medis. */
    case MedicalRecords = 'medical_records';

    /** Persetujuan menampilkan profil publik. */
    case PublicProfile = 'public_profile';

    /** Persetujuan ekspor data. */
    case DataExport = 'data_export';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::DataCollection => 'Pengumpulan Data',
            self::PhotoSharing => 'Berbagi Foto',
            self::MedicalRecords => 'Catatan Medis',
            self::PublicProfile => 'Profil Publik',
            self::DataExport => 'Ekspor Data',
        };
    }

    /**
     * Deskripsi singkat untuk UI.
     */
    public function description(): string
    {
        return match ($this) {
            self::DataCollection => 'Izin mengumpulkan dan menyimpan data pribadi anak.',
            self::PhotoSharing => 'Izin menampilkan dan berbagi foto anak.',
            self::MedicalRecords => 'Izin menyimpan dan mengelola catatan medis anak.',
            self::PublicProfile => 'Izin menampilkan profil anak secara publik.',
            self::DataExport => 'Izin mengekspor data anak ke format lain.',
        };
    }

    /**
     * Apakah consent ini termasuk kategori sensitif?
     */
    public function isSensitive(): bool
    {
        return match ($this) {
            self::MedicalRecords, self::PublicProfile => true,
            default => false,
        };
    }

    /**
     * Semua opsi consent yang tersedia.
     *
     * @return list<self>
     */
    public static function options(): array
    {
        return array_values(self::cases());
    }
}
