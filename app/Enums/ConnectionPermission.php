<?php

namespace App\Enums;

/**
 * Level permission organisasi terhadap data anak melalui Connection.
 *
 * view    — Hanya bisa melihat data
 * comment — Bisa melihat dan memberikan komentar
 * edit    — Bisa melihat, komentar, dan mengedit data
 * manage  — Akses penuh termasuk pengaturan dan administrasi
 */
enum ConnectionPermission: string
{
    /** Hanya bisa melihat data. */
    case View = 'view';

    /** Bisa melihat dan memberikan komentar. */
    case Comment = 'comment';

    /** Bisa melihat, komentar, dan mengedit data. */
    case Edit = 'edit';

    /** Akses penuh termasuk pengaturan dan administrasi. */
    case Manage = 'manage';

    /**
     * Label tampilan dalam Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::View => 'Hanya Lihat',
            self::Comment => 'Lihat & Komentar',
            self::Edit => 'Lihat, Komentar & Edit',
            self::Manage => 'Akses Penuh',
        };
    }

    /**
     * Deskripsi singkat untuk UI.
     */
    public function description(): string
    {
        return match ($this) {
            self::View => 'Bisa melihat data anak, foto, dan timeline.',
            self::Comment => 'Bisa melihat data dan memberikan komentar.',
            self::Edit => 'Bisa melihat, mengomentari, dan mengedit data anak.',
            self::Manage => 'Bisa mengelola semua data termasuk pengaturan dan administrasi.',
        };
    }

    /**
     * Level numerik untuk perbandingan (semakin tinggi = semakin banyak hak akses).
     */
    public function level(): int
    {
        return match ($this) {
            self::View => 1,
            self::Comment => 2,
            self::Edit => 3,
            self::Manage => 4,
        };
    }

    /**
     * Apakah permission ini setidaknya bisa berkomentar?
     */
    public function canComment(): bool
    {
        return $this->level() >= self::Comment->level();
    }

    /**
     * Apakah permission ini setidaknya bisa edit?
     */
    public function canEdit(): bool
    {
        return $this->level() >= self::Edit->level();
    }

    /**
     * Apakah permission ini bisa mengelola semua data (manage)?
     */
    public function canManage(): bool
    {
        return $this->level() >= self::Manage->level();
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
