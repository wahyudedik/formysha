<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

/**
 * Artisan command untuk mengecek dan expire subscription yang sudah lewat masa aktif.
 *
 * Usage:
 *   php artisan subscription:check-expired
 *
 * Schedule:
 *   Harus dijalankan setiap hari via scheduler.
 */
class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscription:check-expired';

    protected $description = 'Cek dan expire subscription yang sudah lewat masa aktif (ends_at < now)';

    public function handle(): int
    {
        $expiredCount = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->update([
                'status' => Subscription::STATUS_PAST_DUE,
            ]);

        if ($expiredCount > 0) {
            $this->info("✅ {$expiredCount} subscription berhasil di-expire (status → past_due).");
        } else {
            $this->info('✅ Tidak ada subscription yang perlu di-expire.');
        }

        // Also deactivate past_due subscriptions after 7 days grace period
        $deactivatedCount = Subscription::where('status', Subscription::STATUS_PAST_DUE)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now()->subDays(7))
            ->update([
                'status' => Subscription::STATUS_INACTIVE,
            ]);

        if ($deactivatedCount > 0) {
            $this->info("⚠️ {$deactivatedCount} subscription di-deactivate setelah 7 hari grace period.");
        }

        return self::SUCCESS;
    }
}
