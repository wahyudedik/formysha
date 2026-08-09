<?php

namespace App\Services;

use App\Mail\SubscriptionMail;
use App\Mail\WelcomeMail;
use App\Models\Child;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Service pusat untuk pembuatan notifikasi (in-app + email).
 *
 * Semua notifikasi di aplikasi harus melewati service ini agar konsisten
 * dan mudah dipelihara. Email dikirim secara async via queue.
 */
class NotificationService
{
    /**
     * Kirim notifikasi selamat datang (in-app + email).
     */
    public function sendWelcome(User $user): Notification
    {
        $notification = $this->createInApp(
            user: $user,
            title: 'Selamat Datang di ForMysha! 🎉',
            message: 'Selamat datang! Mulai dokumentasikan perjalanan hidup buah hati Anda.',
            type: 'success',
            icon: '🎉',
        );

        // Kirim email welcome
        Mail::to($user)->queue(new WelcomeMail($user));

        return $notification;
    }

    /**
     * Kirim notifikasi pengingat imunisasi (in-app + email).
     */
    public function sendImmunizationReminder(
        User $user,
        Child $child,
        string $vaccineName,
        string $dueDate,
    ): Notification {
        $notification = $this->createInApp(
            user: $user,
            title: 'Pengingat Imunisasi 💉',
            message: "Imunisasi {$vaccineName} untuk {$child->name} dijadwalkan pada {$dueDate}.",
            type: 'reminder',
            icon: '💉',
            child: $child,
        );

        return $notification;
    }

    /**
     * Kirim notifikasi pencapaian pertumbuhan (in-app).
     */
    public function sendGrowthMilestone(
        User $user,
        Child $child,
        string $milestone,
    ): Notification {
        return $this->createInApp(
            user: $user,
            title: 'Pencapaian Pertumbuhan! 🌟',
            message: "{$child->name} telah mencapai: {$milestone}",
            type: 'success',
            icon: '🌟',
            child: $child,
        );
    }

    /**
     * Kirim notifikasi status langganan (in-app + email).
     */
    public function sendSubscriptionUpdate(
        User $user,
        string $planName,
        string $status,
    ): Notification {
        $notification = $this->createInApp(
            user: $user,
            title: 'Update Langganan 📋',
            message: "Paket {$planName} Anda saat ini berstatus: {$status}.",
            type: 'info',
            icon: '📋',
        );

        // Kirim email update langganan
        Mail::to($user)->queue(new SubscriptionMail($user, $planName, $status));

        return $notification;
    }

    /**
     * Kirim notifikasi pengingat ulang tahun (in-app).
     */
    public function sendBirthdayReminder(
        User $user,
        Child $child,
        string $birthdayDate,
    ): Notification {
        return $this->createInApp(
            user: $user,
            title: 'Pengingat Ulang Tahun! 🎂',
            message: "Ulang tahun {$child->name} akan datang pada {$birthdayDate}.",
            type: 'reminder',
            icon: '🎂',
            child: $child,
        );
    }

    /**
     * Kirim notifikasi dokumen penting (in-app).
     */
    public function sendDocumentReminder(
        User $user,
        Child $child,
        string $documentType,
    ): Notification {
        return $this->createInApp(
            user: $user,
            title: 'Pengingat Dokumen 📄',
            message: "Dokumen {$documentType} untuk {$child->name} perlu diperbarui.",
            type: 'warning',
            icon: '📄',
            child: $child,
        );
    }

    /**
     * Buat notifikasi in-app.
     */
    public function createInApp(
        User $user,
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = null,
        ?Child $child = null,
        ?string $actionUrl = null,
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'child_id' => $child?->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);
    }
}
