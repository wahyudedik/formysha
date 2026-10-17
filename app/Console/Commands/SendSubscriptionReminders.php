<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminder;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Artisan command untuk mengirim pengingat renewal subscription.
 *
 * Usage:
 *   php artisan subscription:send-reminders
 *
 * Schedule:
 *   Harus dijalankan setiap hari via scheduler.
 */
class SendSubscriptionReminders extends Command
{
    protected $signature = 'subscription:send-reminders';

    protected $description = 'Kirim pengingat renewal untuk subscription yang akan berakhir dalam 7 hari';

    public function handle(): int
    {
        $reminderDays = (int) config('saas.subscription.reminder_days', 7);
        $cutoffDate = now()->addDays($reminderDays);

        $subscriptions = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), $cutoffDate])
            ->with(['tenant', 'plan'])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('✅ Tidak ada subscription yang perlu diingatkan.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($subscriptions as $subscription) {
            $tenant = $subscription->tenant;

            if (! $tenant) {
                continue;
            }

            // Get tenant admin user
            $admin = User::where('tenant_id', $tenant->id)
                ->where('role', 'tenant_admin')
                ->first();

            // Hanya kirim ke user dengan email valid
            if (! $admin || ! filter_var($admin->email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $daysRemaining = (int) now()->diffInDays($subscription->ends_at, false);

            try {
                Mail::to($admin->email)->send(new SubscriptionReminder($subscription, $daysRemaining));

                $this->info("📧 Mengirim pengingat ke {$admin->email} untuk tenant \"{$tenant->name}\" ({$daysRemaining} hari lagi).");

                $sentCount++;
            } catch (\Throwable $e) {
                // Satu kegagalan tidak boleh menghentikan batch.
                $failedCount++;
                $this->error("❌ Gagal mengirim pengingat ke {$admin->email}: {$e->getMessage()}");
            }
        }

        $this->info("✅ {$sentCount} pengingat berhasil dikirim.");

        if ($failedCount > 0) {
            $this->warn("⚠️ {$failedCount} pengingat gagal dikirim.");
        }

        return self::SUCCESS;
    }
}
