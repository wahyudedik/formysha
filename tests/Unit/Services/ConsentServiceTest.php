<?php

use App\Enums\ConsentType;
use App\Models\Child;
use App\Models\Consent;
use App\Models\User;
use App\Services\ConsentService;

describe('ConsentService', function () {
    it('grants consent for a child', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $consent = $service->grant($user, $child, ConsentType::DataCollection, 'Test notes', '127.0.0.1', 'TestAgent');

        expect($consent)->toBeInstanceOf(Consent::class)
            ->and($consent->granted)->toBeTrue()
            ->and($consent->notes)->toBe('Test notes')
            ->and($consent->ip_address)->toBe('127.0.0.1')
            ->and($consent->user_agent)->toBe('TestAgent');
    });

    it('revokes consent for a child', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $service->grant($user, $child, ConsentType::PhotoSharing);
        $result = $service->revoke($user, $child, ConsentType::PhotoSharing);

        expect($result)->not->toBeNull()
            ->and($result->revoked_at)->not->toBeNull();
    });

    it('returns null when revoking non-existent consent', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $result = $service->revoke($user, $child, ConsentType::MedicalRecords);

        expect($result)->toBeNull();
    });

    it('checks consent status correctly', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        // No consent yet
        expect($service->hasConsent($user, $child, ConsentType::DataCollection))->toBeFalse();

        // Grant consent
        $service->grant($user, $child, ConsentType::DataCollection);
        expect($service->hasConsent($user, $child, ConsentType::DataCollection))->toBeTrue();

        // Revoke consent
        $service->revoke($user, $child, ConsentType::DataCollection);
        expect($service->hasConsent($user, $child, ConsentType::DataCollection))->toBeFalse();
    });

    it('gets all consents for a child', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $service->grant($user, $child, ConsentType::DataCollection);
        $service->grant($user, $child, ConsentType::PhotoSharing);

        $consents = $service->getConsents($user, $child);

        expect($consents)->toHaveCount(2);
    });

    it('gets consent statuses for all types', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $service->grant($user, $child, ConsentType::DataCollection);

        $statuses = $service->getConsentStatuses($user, $child);

        expect($statuses)->toHaveCount(5)
            ->and($statuses['data_collection']['granted'])->toBeTrue()
            ->and($statuses['photo_sharing']['granted'])->toBeFalse()
            ->and($statuses['medical_records']['granted'])->toBeFalse()
            ->and($statuses['public_profile']['granted'])->toBeFalse()
            ->and($statuses['data_export']['granted'])->toBeFalse();
    });

    it('grants all consent types', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $service->grantAll($user, $child);

        foreach (ConsentType::cases() as $type) {
            expect($service->hasConsent($user, $child, $type))->toBeTrue();
        }
    });

    it('upserts consent on duplicate grant', function () {
        $service = app(ConsentService::class);
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $service->grant($user, $child, ConsentType::DataCollection, 'First grant');
        $service->grant($user, $child, ConsentType::DataCollection, 'Second grant');

        $count = Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('consent_type', ConsentType::DataCollection)
            ->count();

        expect($count)->toBe(1);

        $consent = Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('consent_type', ConsentType::DataCollection)
            ->first();

        expect($consent->notes)->toBe('Second grant');
    });
});

describe('Consent Model', function () {
    it('is active when granted and not revoked', function () {
        $consent = Consent::factory()->create([
            'granted' => true,
            'revoked_at' => null,
        ]);

        expect($consent->isActive())->toBeTrue();
    });

    it('is not active when revoked', function () {
        $consent = Consent::factory()->create([
            'granted' => true,
            'revoked_at' => now(),
        ]);

        expect($consent->isActive())->toBeFalse();
    });

    it('is not active when not granted', function () {
        $consent = Consent::factory()->create([
            'granted' => false,
            'revoked_at' => null,
        ]);

        expect($consent->isActive())->toBeFalse();
    });

    it('revokes consent and sets revoked_at', function () {
        $consent = Consent::factory()->create([
            'granted' => true,
            'revoked_at' => null,
        ]);

        $consent->revoke();

        expect($consent->refresh()->revoked_at)->not->toBeNull();
    });
});

describe('ConsentType Enum', function () {
    it('returns correct labels', function () {
        expect(ConsentType::DataCollection->label())->toBe('Pengumpulan Data')
            ->and(ConsentType::PhotoSharing->label())->toBe('Berbagi Foto')
            ->and(ConsentType::MedicalRecords->label())->toBe('Catatan Medis')
            ->and(ConsentType::PublicProfile->label())->toBe('Profil Publik')
            ->and(ConsentType::DataExport->label())->toBe('Ekspor Data');
    });

    it('returns correct descriptions', function () {
        expect(ConsentType::DataCollection->description())->toContain('mengumpulkan')
            ->and(ConsentType::PhotoSharing->description())->toContain('foto')
            ->and(ConsentType::MedicalRecords->description())->toContain('medis');
    });

    it('identifies sensitive consents', function () {
        expect(ConsentType::MedicalRecords->isSensitive())->toBeTrue()
            ->and(ConsentType::PublicProfile->isSensitive())->toBeTrue()
            ->and(ConsentType::DataCollection->isSensitive())->toBeFalse()
            ->and(ConsentType::PhotoSharing->isSensitive())->toBeFalse()
            ->and(ConsentType::DataExport->isSensitive())->toBeFalse();
    });

    it('returns all options', function () {
        expect(ConsentType::options())->toHaveCount(5);
    });
});
