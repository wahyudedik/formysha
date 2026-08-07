<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Free Plan
        Plan::create([
            'id' => (string) Str::uuid(),
            'name' => 'Gratis',
            'slug' => 'free',
            'description' => 'Coba ForMysha secara gratis',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_family_members' => 5,
            'max_export_per_day' => 3,
            'features' => ['timeline', 'diary', 'documents', 'growth', 'health'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Basic Plan — Rp 29.000/bulan
        Plan::create([
            'id' => (string) Str::uuid(),
            'name' => 'Basic',
            'slug' => 'basic',
            'description' => 'Paket dasar untuk keluarga kecil',
            'price_monthly' => 29000,
            'price_yearly' => 290000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_family_members' => 10,
            'max_export_per_day' => 10,
            'features' => ['timeline', 'diary', 'documents', 'growth', 'health', 'albums', 'calendar'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Premium Plan — Rp 59.000/bulan
        Plan::create([
            'id' => (string) Str::uuid(),
            'name' => 'Premium',
            'slug' => 'premium',
            'description' => 'Paket premium untuk keluarga besar',
            'price_monthly' => 59000,
            'price_yearly' => 590000,
            'max_children' => 10,
            'max_photos' => 1000,
            'max_videos' => 200,
            'max_storage_mb' => 10240,
            'max_family_members' => 20,
            'max_export_per_day' => 50,
            'features' => ['timeline', 'diary', 'documents', 'growth', 'health', 'albums', 'calendar', 'export', 'public_profile'],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Enterprise Plan — Rp 199.000/bulan
        Plan::create([
            'id' => (string) Str::uuid(),
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Paket enterprise untuk organisasi',
            'price_monthly' => 199000,
            'price_yearly' => 1990000,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => 102400,
            'max_family_members' => -1,
            'max_export_per_day' => -1,
            'features' => ['all'],
            'is_active' => true,
            'sort_order' => 4,
        ]);
    }
}
