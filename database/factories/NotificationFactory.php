<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['reminder', 'info', 'warning', 'success']);
        $title = match ($type) {
            'reminder' => $this->faker->randomElement([
                'Jadwal Imunisasi',
                'Pengingat Ulang Tahun',
                'Jadwal Dokter',
                'Waktunya Minum Obat',
            ]),
            'info' => $this->faker->randomElement([
                'Data Baru Ditambahkan',
                'Profil Diperbarui',
                'Album Baru',
                'Dokumen Baru',
            ]),
            'warning' => $this->faker->randomElement([
                'Dokumen Akan Habis',
                'Kesehatan Perlu Perhatian',
                'Stok Obat Menipis',
                'Jadwal Terlewat',
            ]),
            'success' => $this->faker->randomElement([
                'Imunisasi Selesai',
                'Pertumbuhan Normal',
                'Dokumen Tersimpan',
                'Data Berhasil Diupdate',
            ]),
        };

        $message = match ($type) {
            'reminder' => $this->faker->sentence(),
            'info' => $this->faker->sentence(),
            'warning' => $this->faker->sentence(),
            'success' => $this->faker->sentence(),
            default => $this->faker->sentence(),
        };

        $icon = match ($type) {
            'reminder' => '🔔',
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'success' => '✅',
            default => '📋',
        };

        return [
            'user_id' => User::factory(),
            'child_id' => null,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'action_url' => null,
            'is_read' => false,
            'read_at' => null,
        ];
    }

    /**
     * Mark notification as read.
     */
    public function read(): static
    {
        return $this->state(fn () => [
            'is_read' => true,
            'read_at' => now()->subHour(),
        ]);
    }

    /**
     * Mark notification as unread.
     */
    public function unread(): static
    {
        return $this->state(fn () => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Set specific type.
     */
    public function ofType(string $type): static
    {
        $title = match ($type) {
            'reminder' => 'Jadwal Imunisasi',
            'info' => 'Data Baru Ditambahkan',
            'warning' => 'Dokumen Akan Habis',
            'success' => 'Imunisasi Selesai',
            default => 'Notifikasi Umum',
        };

        $icon = match ($type) {
            'reminder' => '🔔',
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'success' => '✅',
            default => '📋',
        };

        return $this->state(fn () => [
            'type' => $type,
            'title' => $title,
            'icon' => $icon,
        ]);
    }

    /**
     * Attach to a specific child.
     */
    public function forChild(Child $child): static
    {
        return $this->state(fn () => [
            'child_id' => $child->id,
        ]);
    }

    /**
     * With action URL.
     */
    public function withActionUrl(string $url): static
    {
        return $this->state(fn () => [
            'action_url' => $url,
        ]);
    }
}
