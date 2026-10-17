<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Limit Messages (Bahasa Indonesia)
    |--------------------------------------------------------------------------
    |
    | Pesan yang ditampilkan ketika batas paket tercapai. Digunakan oleh
    | EnsureFeatureLimit middleware (web redirect + API JSON 422)
    | dan MediaService::enforceUploadLimits() (ValidationException).
    |
    */

    'children' => 'Anda telah mencapai batas jumlah anak untuk paket Anda. Silakan upgrade paket Anda.',
    'photos' => 'Anda telah mencapai batas foto untuk paket Anda. Silakan upgrade paket Anda.',
    'videos' => 'Anda telah mencapai batas video untuk paket Anda. Silakan upgrade paket Anda.',
    'storage' => 'Anda telah mencapai batas penyimpanan untuk paket Anda. Silakan upgrade paket Anda.',
    'family_members' => 'Anda telah mencapai batas anggota keluarga untuk paket Anda. Silakan upgrade paket Anda.',
    'staff' => 'Anda telah mencapai batas jumlah staf untuk paket Anda. Silakan upgrade paket Anda.',
    'patients' => 'Anda telah mencapai batas jumlah pasien untuk paket Anda. Silakan upgrade paket Anda.',
    'clinical_notes' => 'Anda telah mencapai batas catatan klinis untuk paket Anda. Silakan upgrade paket Anda.',
    'referrals' => 'Anda telah mencapai batas rujukan untuk paket Anda. Silakan upgrade paket Anda.',

    /*
    |--------------------------------------------------------------------------
    | Limit & Feature Labels (Bahasa Indonesia)
    |--------------------------------------------------------------------------
    |
    | Label manusiawi untuk menampilkan limit paket dan isi kolom features
    | (JSON) di halaman plans. Digunakan oleh Plan::featureLabels().
    |
    */

    'unlimited' => 'Tak terbatas',
    'not_included' => 'Tidak termasuk',

    'features' => [
        // Modul B2C
        'timeline' => 'Timeline',
        'diary' => 'Diary',
        'growth' => 'Pertumbuhan',
        'health' => 'Kesehatan',
        'documents' => 'Dokumen',
        'albums' => 'Album',
        'calendar' => 'Kalender',
        'public_profile' => 'Profil Publik',
        'export' => 'Ekspor Data',
        'custom_api' => 'Custom API',
        'priority_support' => 'Dukungan Prioritas',

        // Modul B2B
        'clinical_notes' => 'Catatan Klinis',
        'patient_links' => 'Tautan Pasien',
        'staff_management' => 'Manajemen Staf',
        'referrals' => 'Rujukan',
        'analytics' => 'Analitik',
        'white_label' => 'White Label',
        'multi_location' => 'Multi Lokasi',
        'custom_domain' => 'Domain Kustom',
        'sla' => 'SLA',

        // Limit key-value di kolom features
        'max_staff' => 'Staf Maksimal',
        'max_patients' => 'Pasien Maksimal',
        'max_clinical_notes' => 'Catatan Klinis Maksimal',
        'referrals_enabled' => 'Fitur Rujukan',
    ],

];
