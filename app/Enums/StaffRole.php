<?php

namespace App\Enums;

/**
 * Peran staf dalam tenant B2B (fasilitas kesehatan).
 */
enum StaffRole: string
{
    /** Dokter — bisa menambah catatan klinis, diagnosis, rujukan. */
    case Doctor = 'doctor';

    /** Bidan — bisa menambah catatan persalinan, imunisasi. */
    case Midwife = 'midwife';

    /** Perawat — bisa menambah vital signs, catatan perawatan. */
    case Nurse = 'nurse';

    /** Admin Fasilitas — full access kelola fasilitas, staf, dan pasien. */
    case StaffAdmin = 'staff_admin';

    /** Staf Umum — akses terbatas sesuai kebutuhan. */
    case Staff = 'staff';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Doctor => 'Dokter',
            self::Midwife => 'Bidan',
            self::Nurse => 'Perawat',
            self::StaffAdmin => 'Admin Fasilitas',
            self::Staff => 'Staf',
        };
    }

    /**
     * Apakah staf ini bisa menambah catatan klinis?
     */
    public function canWriteClinicalNotes(): bool
    {
        return in_array($this, [self::Doctor, self::Midwife, self::Nurse]);
    }

    /**
     * Apakah staf ini bisa membuat rujukan?
     */
    public function canCreateReferrals(): bool
    {
        return in_array($this, [self::Doctor, self::Midwife]);
    }

    /**
     * Apakah staf ini punya akses admin penuh?
     */
    public function isAdmin(): bool
    {
        return $this === self::StaffAdmin;
    }

    /**
     * Level akses (semakin tinggi = semakin banyak hak akses).
     */
    public function level(): int
    {
        return match ($this) {
            self::Staff => 1,
            self::Nurse => 2,
            self::Midwife => 3,
            self::Doctor => 4,
            self::StaffAdmin => 5,
        };
    }
}
