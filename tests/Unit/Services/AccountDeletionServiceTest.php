<?php

use App\Models\Child;
use App\Models\Consent;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Timeline;
use App\Models\User;
use App\Services\AccountDeletionService;

describe('AccountDeletionService', function () {
    it('gets correct child data summary', function () {
        $service = app(AccountDeletionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        Timeline::factory()->create(['child_id' => $child->id]);
        Timeline::factory()->create(['child_id' => $child->id]);
        Diary::factory()->create(['child_id' => $child->id]);
        Growth::factory()->create(['child_id' => $child->id]);
        HealthRecord::factory()->create(['child_id' => $child->id]);
        Event::factory()->create(['child_id' => $child->id]);
        Document::factory()->create(['child_id' => $child->id]);
        FamilyMember::factory()->create(['child_id' => $child->id]);
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'data_collection',
            'granted' => true,
        ]);

        $summary = $service->getChildDataSummary($child);

        expect($summary['timelines'])->toBe(2)
            ->and($summary['diaries'])->toBe(1)
            ->and($summary['growths'])->toBe(1)
            ->and($summary['health_records'])->toBe(1)
            ->and($summary['events'])->toBe(1)
            ->and($summary['documents'])->toBe(1)
            ->and($summary['family_members'])->toBe(1)
            ->and($summary['consents'])->toBe(1);
    });

    it('gets correct user data summary', function () {
        $service = app(AccountDeletionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        Child::factory()->create(['user_id' => $user->id]);
        FamilyMember::factory()->create(['child_id' => $child->id]);
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'data_collection',
            'granted' => true,
        ]);

        $summary = $service->getUserDataSummary($user);

        expect($summary['children'])->toBe(2)
            ->and($summary['family_members'])->toBe(1)
            ->and($summary['consents'])->toBe(1);
    });

    it('deletes child data and returns results', function () {
        $service = app(AccountDeletionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        Timeline::factory()->create(['child_id' => $child->id]);
        Diary::factory()->create(['child_id' => $child->id]);

        $result = $service->deleteChildData($child);

        expect($result['deleted'])->toBe(1);
        $this->assertDatabaseMissing('children', ['id' => $child->id]);
        $this->assertDatabaseCount('timelines', 0);
        $this->assertDatabaseCount('diaries', 0);
    });

    it('deletes user data and returns results', function () {
        $service = app(AccountDeletionService::class);
        $user = User::factory()->create();
        Child::factory()->create(['user_id' => $user->id]);
        Child::factory()->create(['user_id' => $user->id]);

        $result = $service->deleteUserData($user);

        expect($result['children_deleted'])->toBe(2);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });

    it('deletes consent records when deleting child', function () {
        $service = app(AccountDeletionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'data_collection',
            'granted' => true,
        ]);

        $service->deleteChildData($child);

        $this->assertDatabaseCount('consents', 0);
    });

    it('deletes family members when deleting child', function () {
        $service = app(AccountDeletionService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        FamilyMember::factory()->create(['child_id' => $child->id]);
        FamilyMember::factory()->create(['child_id' => $child->id]);

        $service->deleteChildData($child);

        $this->assertDatabaseCount('family_members', 0);
    });
});
