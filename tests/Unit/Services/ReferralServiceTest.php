<?php

use App\Enums\ReferralStatus;
use App\Enums\ReferralType;
use App\Models\Child;
use App\Models\Referral;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ReferralService;

describe('ReferralService', function () {
    it('creates a facility-to-facility referral', function () {
        $service = app(ReferralService::class);
        $child = Child::factory()->create();
        $from = Tenant::create(['name' => 'RS From', 'slug' => 'rs-from-'.rand(1, 9999)]);
        $to = Tenant::create(['name' => 'RS To', 'slug' => 'rs-to-'.rand(1, 9999)]);
        $staff = User::factory()->create();

        $referral = $service->createFacilityReferral(
            $child,
            $from,
            $to,
            $staff,
            'Rujukan spesialis',
            'Ringkasan klinis',
            'Catatan tambahan'
        );

        expect($referral)->not->toBeNull();
        expect($referral->child_id)->toBe($child->id);
        expect($referral->from_tenant_id)->toBe($from->id);
        expect($referral->to_tenant_id)->toBe($to->id);
        expect($referral->referring_staff_id)->toBe($staff->id);
        expect($referral->status)->toBe(ReferralStatus::Pending);
        expect($referral->type)->toBe(ReferralType::FacilityToFacility);
        expect($referral->reason)->toBe('Rujukan spesialis');
    });

    it('creates a facility-to-family referral', function () {
        $service = app(ReferralService::class);
        $child = Child::factory()->create();
        $from = Tenant::create(['name' => 'RS Family', 'slug' => 'rs-family-'.rand(1, 9999)]);

        $referral = $service->createFamilyReferral(
            $child,
            $from,
            'parent@email.com',
            '08123456789',
            'Kontrol rutin'
        );

        expect($referral)->not->toBeNull();
        expect($referral->child_id)->toBe($child->id);
        expect($referral->from_tenant_id)->toBe($from->id);
        expect($referral->status)->toBe(ReferralStatus::Pending);
        expect($referral->type)->toBe(ReferralType::FacilityToFamily);
    });

    it('accepts a referral', function () {
        $service = app(ReferralService::class);
        $child = Child::factory()->create();
        $from = Tenant::create(['name' => 'RS Accept', 'slug' => 'rs-accept-'.rand(1, 9999)]);
        $to = Tenant::create(['name' => 'RS Accept To', 'slug' => 'rs-accept-to-'.rand(1, 9999)]);
        $staff = User::factory()->create();
        $referral = $service->createFacilityReferral($child, $from, $to, $staff, 'Test');

        $service->acceptReferral($referral);

        $referral->refresh();
        expect($referral->status)->toBe(ReferralStatus::Accepted);
    });

    it('completes a referral', function () {
        $service = app(ReferralService::class);
        $child = Child::factory()->create();
        $from = Tenant::create(['name' => 'RS Complete', 'slug' => 'rs-complete-'.rand(1, 9999)]);
        $to = Tenant::create(['name' => 'RS Complete To', 'slug' => 'rs-complete-to-'.rand(1, 9999)]);
        $staff = User::factory()->create();
        $referral = $service->createFacilityReferral($child, $from, $to, $staff, 'Test');
        $service->acceptReferral($referral);

        $service->completeReferral($referral);

        $referral->refresh();
        expect($referral->status)->toBe(ReferralStatus::Completed);
    });

    it('cancels a referral', function () {
        $service = app(ReferralService::class);
        $child = Child::factory()->create();
        $from = Tenant::create(['name' => 'RS Cancel', 'slug' => 'rs-cancel-'.rand(1, 9999)]);
        $to = Tenant::create(['name' => 'RS Cancel To', 'slug' => 'rs-cancel-to-'.rand(1, 9999)]);
        $staff = User::factory()->create();
        $referral = $service->createFacilityReferral($child, $from, $to, $staff, 'Test');

        $service->cancelReferral($referral);

        $referral->refresh();
        expect($referral->status)->toBe(ReferralStatus::Cancelled);
    });

    it('gets referral stats for a tenant', function () {
        $service = app(ReferralService::class);
        $tenant = Tenant::create(['name' => 'RS Stats', 'slug' => 'rs-stats-'.rand(1, 9999)]);
        $otherTenant = Tenant::create(['name' => 'RS Other', 'slug' => 'rs-other-'.rand(1, 9999)]);
        $child = Child::factory()->create();
        $staff = User::factory()->create();

        // Create referrals sent by this tenant
        $service->createFacilityReferral($child, $tenant, $otherTenant, $staff, 'Pending referral');
        $accepted = $service->createFacilityReferral($child, $tenant, $otherTenant, $staff, 'Accepted referral');
        $service->acceptReferral($accepted);
        $completed = $service->createFacilityReferral($child, $tenant, $otherTenant, $staff, 'Completed referral');
        $service->acceptReferral($completed);
        $service->completeReferral($completed);

        // Create referral received by this tenant
        $service->createFacilityReferral($child, $otherTenant, $tenant, $staff, 'Received referral');

        $stats = $service->getReferralStats($tenant);

        expect($stats['sent'])->toBe(3);
        expect($stats['received'])->toBe(1);
        expect($stats['pending'])->toBe(1);
        expect($stats['accepted'])->toBe(1);
        expect($stats['completed'])->toBe(1);
    });

    it('gets reward milestones', function () {
        $service = app(ReferralService::class);
        $tenant = Tenant::create(['name' => 'RS Milestone', 'slug' => 'rs-milestone-'.rand(1, 9999)]);

        $result = $service->getRewardMilestones($tenant);

        expect($result)->toHaveKey('total_completed');
        expect($result)->toHaveKey('milestones');
        expect($result['total_completed'])->toBe(0);
        expect($result['milestones'])->toHaveCount(4);
        expect($result['milestones'][0]['title'])->toBe('Pemula');
        expect($result['milestones'][0]['unlocked'])->toBeFalse();
    });

    it('gets recent referrals', function () {
        $service = app(ReferralService::class);
        $tenant = Tenant::create(['name' => 'RS Recent', 'slug' => 'rs-recent-'.rand(1, 9999)]);
        $otherTenant = Tenant::create(['name' => 'RS Recent2', 'slug' => 'rs-recent2-'.rand(1, 9999)]);
        $child = Child::factory()->create();
        $staff = User::factory()->create();

        $service->createFacilityReferral($child, $tenant, $otherTenant, $staff, 'Recent referral');

        $referrals = $service->getRecentReferrals($tenant);

        expect($referrals->count())->toBe(1);
    });
});
