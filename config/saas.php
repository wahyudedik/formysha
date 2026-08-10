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
    | Plan Limits
    |--------------------------------------------------------------------------
    |
    | Batas default untuk setiap paket langganan.
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

];
