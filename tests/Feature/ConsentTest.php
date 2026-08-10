<?php

use App\Enums\ConsentType;
use App\Models\Child;
use App\Models\Consent;
use App\Models\User;
use App\Services\ConsentService;

describe('Consent Management', function () {
    it('shows consent index for child owner', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('consent.index', $child))
            ->assertOk()
            ->assertSee('Pengaturan Privasi')
            ->assertSee('Pengumpulan Data')
            ->assertSee('Berbagi Foto');
    });

    it('prevents viewing other users consent page', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->get(route('consent.index', $child))
            ->assertForbidden();
    });

    it('grants consent successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('consent.update', $child), [
                'consent_type' => 'data_collection',
                'action' => 'grant',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('consents', [
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'data_collection',
            'granted' => true,
        ]);
    });

    it('revokes consent successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        // First grant
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'photo_sharing',
            'granted' => true,
        ]);

        $this->actingAs($user)
            ->post(route('consent.update', $child), [
                'consent_type' => 'photo_sharing',
                'action' => 'revoke',
            ])
            ->assertRedirect();

        $consent = Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('consent_type', 'photo_sharing')
            ->first();

        $this->assertNotNull($consent->revoked_at);
    });

    it('prevents other users from granting consent', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->post(route('consent.update', $child), [
                'consent_type' => 'data_collection',
                'action' => 'grant',
            ])
            ->assertForbidden();
    });

    it('validates consent type', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('consent.update', $child), [
                'consent_type' => 'invalid_type',
                'action' => 'grant',
            ])
            ->assertSessionHasErrors('consent_type');
    });

    it('validates action', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('consent.update', $child), [
                'consent_type' => 'data_collection',
                'action' => 'invalid_action',
            ])
            ->assertSessionHasErrors('action');
    });

    it('shows all five consent types on index', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('consent.index', $child));

        $response->assertOk()
            ->assertSee('Pengumpulan Data')
            ->assertSee('Berbagi Foto')
            ->assertSee('Catatan Medis')
            ->assertSee('Profil Publik')
            ->assertSee('Ekspor Data');
    });

    it('displays summary bar with correct counts', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        // Grant 2 consents
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'data_collection',
            'granted' => true,
        ]);
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'photo_sharing',
            'granted' => true,
        ]);

        $this->actingAs($user)
            ->get(route('consent.index', $child))
            ->assertOk()
            ->assertSee('2/5');
    });

    it('marks sensitive consents with badge', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('consent.index', $child))
            ->assertOk()
            ->assertSee('Sensitif');
    });

    it('grants all consent types via grantAll', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $consentService = app(ConsentService::class);
        $consentService->grantAll($user, $child, '127.0.0.1');

        $this->assertCount(5, Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->get());

        foreach (ConsentType::cases() as $type) {
            $this->assertDatabaseHas('consents', [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'consent_type' => $type->value,
                'granted' => true,
            ]);
        }
    });
});
