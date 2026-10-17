<?php

use App\Models\Plan;
use App\Models\User;

describe('Plans Page Polish', function () {
    it('displays family and facility audience sections with headings', function () {
        $user = User::factory()->create();

        Plan::create([
            'name' => 'Keluarga Ceria',
            'slug' => 'keluarga-ceria',
            'price_monthly' => 19000,
            'max_children' => 2,
            'max_photos' => 100,
            'max_videos' => 20,
            'max_storage_mb' => 1024,
            'max_export_per_day' => 5,
            'is_active' => true,
            'audience' => Plan::AUDIENCE_FAMILY,
            'sort_order' => 1,
        ]);

        Plan::create([
            'name' => 'Klinik Sehat',
            'slug' => 'klinik-sehat',
            'price_monthly' => 299000,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => 102400,
            'max_export_per_day' => -1,
            'is_active' => true,
            'audience' => Plan::AUDIENCE_FACILITY,
            'sort_order' => 2,
        ]);

        $this->actingAs($user)
            ->get(route('subscription.plans'))
            ->assertOk()
            ->assertSee(__('subscription.section_family_title'))
            ->assertSee(__('subscription.section_family_description'))
            ->assertSee(__('subscription.section_facility_title'))
            ->assertSee(__('subscription.section_facility_description'));
    });

    it('marks the recommended family plan with the POPULER badge only', function () {
        $user = User::factory()->create();

        Plan::create([
            'name' => 'Family Plus',
            'slug' => Plan::POPULAR_SLUG,
            'price_monthly' => 39000,
            'max_children' => 5,
            'max_photos' => 500,
            'max_videos' => 100,
            'max_storage_mb' => 10240,
            'max_export_per_day' => 20,
            'is_active' => true,
            'audience' => Plan::AUDIENCE_FAMILY,
            'sort_order' => 1,
        ]);

        Plan::create([
            'name' => 'Family Basic',
            'slug' => 'family-basic-test',
            'price_monthly' => 19000,
            'max_children' => 2,
            'max_photos' => 100,
            'max_videos' => 20,
            'max_storage_mb' => 1024,
            'max_export_per_day' => 5,
            'is_active' => true,
            'audience' => Plan::AUDIENCE_FAMILY,
            'sort_order' => 2,
        ]);

        Plan::create([
            'name' => 'B2B Test',
            'slug' => 'b2b-test',
            'price_monthly' => 299000,
            'max_children' => -1,
            'max_photos' => -1,
            'max_videos' => -1,
            'max_storage_mb' => 102400,
            'max_export_per_day' => -1,
            'is_active' => true,
            'audience' => Plan::AUDIENCE_FACILITY,
            'sort_order' => 3,
        ]);

        $this->actingAs($user)
            ->get(route('subscription.plans'))
            ->assertOk()
            ->assertSee('data-plan-popular="'.Plan::POPULAR_SLUG.'"', false)
            ->assertDontSee('data-plan-popular="family-basic-test"', false)
            ->assertDontSee('data-plan-popular="b2b-test"', false);
    });

    it('shows FAQ answers matching the actual subscription behaviour', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('subscription.plans'))
            ->assertOk()
            ->assertSee(__('subscription.faq_payment_a'))
            ->assertSee(__('subscription.faq_switch_a'))
            ->assertSee(__('subscription.faq_inactive_a'));
    });
});
