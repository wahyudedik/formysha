<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SaaS Mode
    |--------------------------------------------------------------------------
    |
    | Menentukan apakah aplikasi berjalan dalam mode SaaS (multi-tenant)
    | atau single-tenant mode.
    |
    */

    'mode' => env('SAAS_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Bank Accounts
    |--------------------------------------------------------------------------
    |
    | Informasi rekening bank untuk pembayaran manual transfer.
    |
    */

    'banks' => [
        'BRI' => [
            'account' => env('BILLING_BRI_ACCOUNT', ''),
            'holder' => env('BILLING_BRI_HOLDER', ''),
        ],
        'JAGO' => [
            'account' => env('BILLING_JAGO_ACCOUNT', ''),
            'holder' => env('BILLING_JAGO_HOLDER', ''),
        ],
        'BTN' => [
            'account' => env('BILLING_BTN_ACCOUNT', ''),
            'holder' => env('BILLING_BTN_HOLDER', ''),
        ],
        'BSI' => [
            'account' => env('BILLING_BSI_ACCOUNT', ''),
            'holder' => env('BILLING_BSI_HOLDER', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | B2C Plan Limits (Family/Individual)
    |--------------------------------------------------------------------------
    |
    | Batas default untuk paket langganan B2C (keluarga/individu).
    |
    */

    'plans' => [
        'free' => [
            'name' => 'Gratis',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'max_children' => 1,
            'max_photos' => 50,
            'max_videos' => 10,
            'max_storage_mb' => 500,
            'max_family_members' => 5,
            'max_export_per_day' => 3,
        ],
        'basic' => [
            'name' => 'Basic',
            'price_monthly' => 29000,
            'price_yearly' => 290000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_family_members' => 10,
            'max_export_per_day' => 10,
        ],
        'premium' => [
            'name' => 'Premium',
            'price_monthly' => 59000,
            'price_yearly' => 590000,
            'max_children' => 10,
            'max_photos' => 1000,
            'max_videos' => 200,
            'max_storage_mb' => 10240,
            'max_family_members' => 20,
            'max_export_per_day' => 50,
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price_monthly' => 199000,
            'price_yearly' => 1990000,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => 102400,
            'max_family_members' => -1,
            'max_export_per_day' => -1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | B2B Plan Limits (Facilities)
    |--------------------------------------------------------------------------
    |
    | Batas default untuk paket langganan B2B (fasilitas kesehatan/pendidikan).
    | Field tambahan: max_staff, max_patients, max_clinical_notes, referrals_enabled.
    |
    */

    'b2b_plans' => [
        'facility_starter' => [
            'name' => 'Fasilitas Starter',
            'price_monthly' => 199000,
            'price_yearly' => 1990000,
            'max_children' => 50,
            'max_photos' => 500,
            'max_videos' => 50,
            'max_storage_mb' => 5120,
            'max_family_members' => 100,
            'max_export_per_day' => 20,
            'max_staff' => 5,
            'max_patients' => 50,
            'max_clinical_notes' => 500,
            'referrals_enabled' => false,
        ],
        'facility_pro' => [
            'name' => 'Fasilitas Pro',
            'price_monthly' => 499000,
            'price_yearly' => 4990000,
            'max_children' => 500,
            'max_photos' => 5000,
            'max_videos' => 500,
            'max_storage_mb' => 51200,
            'max_family_members' => 1000,
            'max_export_per_day' => 100,
            'max_staff' => 25,
            'max_patients' => 500,
            'max_clinical_notes' => 5000,
            'referrals_enabled' => true,
        ],
        'facility_enterprise' => [
            'name' => 'Fasilitas Enterprise',
            'price_monthly' => 999000,
            'price_yearly' => 9990000,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => 102400,
            'max_family_members' => -1,
            'max_export_per_day' => -1,
            'max_staff' => -1,
            'max_patients' => -1,
            'max_clinical_notes' => -1,
            'referrals_enabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan terkait langganan seperti masa trial dan grace period.
    |
    */

    'subscription' => [
        'trial_days' => 14,
        'grace_period_days' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan default untuk tenant baru.
    |
    */

    'defaults' => [
        'timezone' => 'Asia/Jakarta',
        'locale' => 'id',
        'currency' => 'IDR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Types
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk tipe tenant B2C dan B2B.
    |
    */

    'tenant_types' => [
        'b2c' => ['family'],
        'b2b' => ['hospital', 'clinic', 'midwifery', 'posyandu', 'daycare', 'school'],
    ],

];
