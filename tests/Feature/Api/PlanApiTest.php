<?php

use App\Models\Plan;

describe('Plan API', function () {
    it('can list plans publicly', function () {
        Plan::create([
            'name' => 'Free Plan',
            'slug' => 'free-plan',
            'price_monthly' => 0,
            'max_children' => 1,
            'max_photos' => 10,
            'max_videos' => 5,
            'max_storage_mb' => 500,
            'max_export_per_day' => 1,
            'is_active' => true,
        ]);
        Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'price_monthly' => 29000,
            'max_children' => 3,
            'max_photos' => 200,
            'max_videos' => 50,
            'max_storage_mb' => 2048,
            'max_export_per_day' => 10,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/plans');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    });

    it('can show plan detail', function () {
        $plan = Plan::create([
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'price_monthly' => 59000,
            'max_children' => 10,
            'max_photos' => 1000,
            'max_videos' => 200,
            'max_storage_mb' => 10240,
            'max_export_per_day' => 20,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/plans/'.$plan->id);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Premium Plan');
    });
});
