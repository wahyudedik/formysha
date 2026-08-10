<?php

namespace App\Enums;

/**
 * Level permission anggota keluarga terhadap data anak.
 *
 * view   — Hanya bisa melihat data (kakek, nenek, teman)
 * edit   — Bisa menambah/mengedit data (pasangan, saudara)
 * admin  — Bisa mengelola semua data (co-parent)
 */
enum FamilyMemberPermission: string
{
    /** Hanya bisa melihat data. */
    case View = 'view';

    /** Bisa menambah dan mengedit data. */
    case Edit = 'edit';

    /** Bisa mengelola semua data termasuk hapus. */
    case Admin = 'admin';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::View => 'Hanya Lihat',
            self::Edit => 'Lihat & Edit',
            self::Admin => 'Admin Penuh',
        };
    }

    /**
     * Deskripsi singkat untuk UI.
     */
    public function description(): string
    {
        return match ($this) {
            self::View => 'Bisa melihat data anak, foto, dan timeline.',
            self::Edit => 'Bisa melihat, menambah, dan mengedit data anak.',
            self::Admin => 'Bisa mengelola semua data termasuk hapus dan pengaturan.',
        };
    }

    /**
     * Level numerik untuk perbandingan (semakin tinggi = semakin banyak hak akses).
     */
    public function level(): int
    {
        return match ($this) {
            self::View => 1,
            self::Edit => 2,
            self::Admin => 3,
        };
    }

    /**
     * Apakah permission ini setidaknya bisa edit?
     */
    public function canEdit(): bool
    {
        return $this->level() >= self::Edit->level();
    }

    /**
     * Apakah permission ini bisa mengelola semua data (admin)?
     */
    public function canManage(): bool
    {
        return $this->level() >= self::Admin->level();
    }

    /**
     * Semua opsi permission yang tersedia.
     *
     * @return list<self>
     */
    public static function options(): array
    {
        return self::cases();
    }
}
