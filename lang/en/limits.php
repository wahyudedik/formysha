<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Limit Messages (English)
    |--------------------------------------------------------------------------
    |
    | Messages displayed when a plan limit has been reached. Used by the
    | EnsureFeatureLimit middleware (web redirect + API JSON 422)
    | and MediaService::enforceUploadLimits() (ValidationException).
    |
    */

    'children' => 'You have reached the maximum number of children for your plan. Please upgrade your plan.',
    'photos' => 'You have reached the photo limit for your plan. Please upgrade your plan.',
    'videos' => 'You have reached the video limit for your plan. Please upgrade your plan.',
    'storage' => 'You have reached the storage limit for your plan. Please upgrade your plan.',
    'family_members' => 'You have reached the family members limit for your plan. Please upgrade your plan.',
    'staff' => 'You have reached the staff limit for your plan. Please upgrade your plan.',
    'patients' => 'You have reached the patients limit for your plan. Please upgrade your plan.',
    'clinical_notes' => 'You have reached the clinical notes limit for your plan. Please upgrade your plan.',
    'referrals' => 'You have reached the referrals limit for your plan. Please upgrade your plan.',

    /*
    |--------------------------------------------------------------------------
    | Limit & Feature Labels (English)
    |--------------------------------------------------------------------------
    |
    | Human-readable labels for plan limits and the features JSON column
    | shown on the plans page. Used by Plan::featureLabels().
    |
    */

    'unlimited' => 'Unlimited',
    'not_included' => 'Not included',

    'features' => [
        // B2C modules
        'timeline' => 'Timeline',
        'diary' => 'Diary',
        'growth' => 'Growth',
        'health' => 'Health',
        'documents' => 'Documents',
        'albums' => 'Albums',
        'calendar' => 'Calendar',
        'public_profile' => 'Public Profile',
        'export' => 'Data Export',
        'custom_api' => 'Custom API',
        'priority_support' => 'Priority Support',

        // B2B modules
        'clinical_notes' => 'Clinical Notes',
        'patient_links' => 'Patient Links',
        'staff_management' => 'Staff Management',
        'referrals' => 'Referrals',
        'analytics' => 'Analytics',
        'white_label' => 'White Label',
        'multi_location' => 'Multi Location',
        'custom_domain' => 'Custom Domain',
        'sla' => 'SLA',

        // Key-value limits inside the features column
        'max_staff' => 'Max Staff',
        'max_patients' => 'Max Patients',
        'max_clinical_notes' => 'Max Clinical Notes',
        'referrals_enabled' => 'Referrals Feature',
    ],

];
