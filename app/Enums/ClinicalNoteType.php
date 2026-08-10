<?php

namespace App\Enums;

/**
 * Tipe catatan klinis yang dapat dibuat oleh staf medis.
 */
enum ClinicalNoteType: string
{
    /** Konsultasi — kunjungan rutin atau konsultasi umum. */
    case Consultation = 'consultation';

    /** Pemeriksaan — pemeriksaan fisik atau penunjang. */
    case Examination = 'examination';

    /** Penanganan — tindakan medis atau pengobatan. */
    case Treatment = 'treatment';

    /** Tindak lanjut — kunjungan follow-up setelah penanganan. */
    case FollowUp = 'follow-up';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Consultation => 'Konsultasi',
            self::Examination => 'Pemeriksaan',
            self::Treatment => 'Penanganan',
            self::FollowUp => 'Tindak Lanjut',
        };
    }
}
