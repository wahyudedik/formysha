<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MilestoneService;
use Illuminate\Console\Command;

/**
 * Artisan command untuk mengecek dan membuat milestone alerts.
 *
 * Usage:
 *   php artisan milestone:check
 *
 * Schedule:
 *   Harus dijalankan setiap hari via scheduler.
 */
class CheckMilestones extends Command
{
    protected $signature = 'milestone:check {--user-id= : Check milestones for a specific user only}';

    protected $description = 'Cek dan buat milestone alerts untuk semua anak aktif';

    public function handle(MilestoneService $milestoneService): int
    {
        $userId = $this->option('user-id');

        if ($userId) {
            $users = User::where('id', $userId)->get();
        } else {
            $users = User::has('children')->get();
        }

        $totalAlerts = 0;

        foreach ($users as $user) {
            $alerts = $milestoneService->checkAllChildren($user);
            $totalAlerts += count($alerts);
        }

        if ($totalAlerts > 0) {
            $this->info("✅ {$totalAlerts} milestone alert baru berhasil dibuat.");
        } else {
            $this->info('✅ Tidak ada milestone alert baru.');
        }

        return self::SUCCESS;
    }
}
