<?php

use App\Models\Plan;

describe('Plan Feature Labels', function () {
    it('translates module feature strings into human labels', function () {
        $plan = new Plan;
        $plan->features = ['timeline', 'diary'];

        expect($plan->featureLabels())
            ->toContain(__('limits.features.timeline'))
            ->toContain(__('limits.features.diary'));
    });

    it('renders unlimited for null and -1 feature values', function () {
        $plan = new Plan;
        $plan->features = ['max_staff' => null, 'max_patients' => -1];

        expect($plan->featureLabels())
            ->toContain(__('limits.features.max_staff').' — '.__('limits.unlimited'))
            ->toContain(__('limits.features.max_patients').' — '.__('limits.unlimited'));
    });

    it('formats numeric feature limits and marks excluded booleans', function () {
        $plan = new Plan;
        $plan->features = ['max_staff' => 5, 'referrals_enabled' => false, 'analytics' => true];

        expect($plan->featureLabels())
            ->toContain(__('limits.features.max_staff').': 5')
            ->toContain(__('limits.features.referrals_enabled').' — '.__('limits.not_included'))
            ->toContain(__('limits.features.analytics'));
    });

    it('falls back to headline formatting when no translation exists', function () {
        $plan = new Plan;
        $plan->features = ['some_unknown_feature'];

        expect($plan->featureLabels())->toContain('Some Unknown Feature');
    });
});

describe('Plan Popularity', function () {
    it('is popular only for the recommended family plan slug', function () {
        $recommended = new Plan;
        $recommended->slug = Plan::POPULAR_SLUG;
        $recommended->audience = Plan::AUDIENCE_FAMILY;
        expect($recommended->isPopular())->toBeTrue();

        $facilityPlan = new Plan;
        $facilityPlan->slug = Plan::POPULAR_SLUG;
        $facilityPlan->audience = Plan::AUDIENCE_FACILITY;
        expect($facilityPlan->isPopular())->toBeFalse();

        $otherFamilyPlan = new Plan;
        $otherFamilyPlan->slug = 'family-basic';
        $otherFamilyPlan->audience = Plan::AUDIENCE_FAMILY;
        expect($otherFamilyPlan->isPopular())->toBeFalse();
    });
});
