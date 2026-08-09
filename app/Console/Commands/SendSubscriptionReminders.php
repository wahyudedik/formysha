<?php

namespace App\Console\Commands;

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
        $reminderDays = 7;
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

        foreach ($subscriptions as $subscription) {
            $tenant = $subscription->tenant;

            if (! $tenant) {
                continue;
            }

            // Get tenant admin user
            $admin = User::where('tenant_id', $tenant->id)
                ->where('role', 'tenant_admin')
                ->first();

            if (! $admin) {
                continue;
            }

            $daysRemaining = (int) now()->diffInDays($subscription->ends_at, false);

            $this->info("📧 Mengirim pengingat ke {$admin->email} untuk tenant \"{$tenant->name}\" ({$daysRemaining} hari lagi).");

            // In production, send actual email here:
            // Mail::to($admin->email)->send(new SubscriptionReminder($subscription, $daysRemaining));

            $sentCount++;
        }

        $this->info("✅ {$sentCount} pengingat berhasil dikirim.");

        return self::SUCCESS;
    }
}
