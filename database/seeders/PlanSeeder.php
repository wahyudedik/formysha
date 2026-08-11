<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * B2C Plans: Free, Family, Family Plus, Family Pro
     * B2B Plans: B2B Basic, B2B Growth, B2B Pro, Enterprise
     */
    public function run(): void
    {
        // ============================================================
        // B2C PLANS (Family)
        // ============================================================

        // Free Plan — Gratis
        Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Gratis',
                'description' => 'Coba ForMysha secara gratis — cocok untuk satu anak',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_children' => 1,
                'max_photos' => 10,
                'max_videos' => 5,
                'max_storage_mb' => 500,
                'max_family_members' => 2,
                'max_export_per_day' => 1,
                'features' => [
                    'timeline', 'diary', 'growth', 'health',
                    'public_profile' => false,
                ],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // Family Plan — Rp 19.000/bulan
        Plan::firstOrCreate(
            ['slug' => 'family'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Family',
                'description' => 'Paket keluarga untuk menyimpan kenangan lebih banyak',
                'price_monthly' => 19000,
                'price_yearly' => 190000,
                'max_children' => 3,
                'max_photos' => 200,
                'max_videos' => 50,
                'max_storage_mb' => 5120,
                'max_family_members' => 5,
                'max_export_per_day' => 5,
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'public_profile',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        // Family Plus Plan — Rp 39.000/bulan
        Plan::firstOrCreate(
            ['slug' => 'family-plus'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Family Plus',
                'description' => 'Paket keluarga plus untuk dokumentasi lebih lengkap',
                'price_monthly' => 39000,
                'price_yearly' => 390000,
                'max_children' => 5,
                'max_photos' => 500,
                'max_videos' => 150,
                'max_storage_mb' => 15360,
                'max_family_members' => 10,
                'max_export_per_day' => 15,
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'export', 'public_profile',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // Family Pro Plan — Rp 79.000/bulan
        Plan::firstOrCreate(
            ['slug' => 'family-pro'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Family Pro',
                'description' => 'Paket keluarga pro untuk dokumentasi tanpa batas',
                'price_monthly' => 79000,
                'price_yearly' => 790000,
                'max_children' => 10,
                'max_photos' => -1, // unlimited
                'max_videos' => -1, // unlimited
                'max_storage_mb' => 51200,
                'max_family_members' => 20,
                'max_export_per_day' => -1, // unlimited
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'export', 'public_profile',
                    'custom_api', 'priority_support',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        // ============================================================
        // B2B PLANS (Organization)
        // ============================================================

        // B2B Basic Plan — Rp 299.000/bulan
        Plan::firstOrCreate(
            ['slug' => 'b2b-basic'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'B2B Basic',
                'description' => 'Paket dasar untuk klinik atau sekolah kecil',
                'price_monthly' => 299000,
                'price_yearly' => 2990000,
                'max_children' => 50,
                'max_photos' => 500,
                'max_videos' => 100,
                'max_storage_mb' => 10240,
                'max_family_members' => -1,
                'max_export_per_day' => 30,
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'export', 'clinical_notes',
                    'patient_links', 'staff_management',
                    'max_staff' => 5,
                    'max_patients' => 50,
                    'referrals_enabled' => false,
                    'custom_domain' => false,
                    'white_label' => false,
                ],
                'is_active' => true,
                'sort_order' => 5,
            ]
        );

        // B2B Growth Plan — Rp 799.000/bulan
        Plan::firstOrCreate(
            ['slug' => 'b2b-growth'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'B2B Growth',
                'description' => 'Paket pertumbuhan untuk rumah sakit atau sekolah menengah',
                'price_monthly' => 799000,
                'price_yearly' => 7990000,
                'max_children' => 200,
                'max_photos' => 2000,
                'max_videos' => 500,
                'max_storage_mb' => 51200,
                'max_family_members' => -1,
                'max_export_per_day' => 100,
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'export', 'clinical_notes',
                    'patient_links', 'staff_management', 'referrals',
                    'analytics',
                    'max_staff' => 20,
                    'max_patients' => 200,
                    'referrals_enabled' => true,
                    'custom_domain' => false,
                    'white_label' => false,
                ],
                'is_active' => true,
                'sort_order' => 6,
            ]
        );

        // B2B Pro Plan — Rp 1.999.000/bulan
        Plan::firstOrCreate(
            ['slug' => 'b2b-pro'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'B2B Pro',
                'description' => 'Paket profesional untuk rumah sakit atau jaringan sekolah',
                'price_monthly' => 1999000,
                'price_yearly' => 19990000,
                'max_children' => 1000,
                'max_photos' => -1, // unlimited
                'max_videos' => -1, // unlimited
                'max_storage_mb' => 204800,
                'max_family_members' => -1,
                'max_export_per_day' => -1, // unlimited
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'export', 'clinical_notes',
                    'patient_links', 'staff_management', 'referrals',
                    'analytics', 'custom_api', 'white_label', 'multi_location',
                    'max_staff' => -1, // unlimited
                    'max_patients' => 1000,
                    'referrals_enabled' => true,
                    'custom_domain' => true,
                    'white_label' => true,
                ],
                'is_active' => true,
                'sort_order' => 7,
            ]
        );

        // Enterprise Plan — Custom pricing
        Plan::firstOrCreate(
            ['slug' => 'enterprise'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Enterprise',
                'description' => 'Paket enterprise dengan harga custom — untuk organisasi besar',
                'price_monthly' => 0, // custom — diatur manual oleh Super Admin
                'price_yearly' => 0, // custom
                'max_children' => -1, // unlimited
                'max_photos' => -1, // unlimited
                'max_videos' => -1, // unlimited
                'max_storage_mb' => -1, // unlimited
                'max_family_members' => -1, // unlimited
                'max_export_per_day' => -1, // unlimited
                'features' => [
                    'timeline', 'diary', 'documents', 'growth', 'health',
                    'albums', 'calendar', 'export', 'clinical_notes',
                    'patient_links', 'staff_management', 'referrals',
                    'analytics', 'custom_api', 'white_label', 'multi_location',
                    'custom_domain', 'priority_support', 'sla',
                    'max_staff' => -1, // unlimited
                    'max_patients' => -1, // unlimited
                    'referrals_enabled' => true,
                    'custom_domain' => true,
                    'white_label' => true,
                ],
                'is_active' => true,
                'sort_order' => 8,
            ]
        );
    }
}
