<?php

use App\Models\Child;
use App\Models\Consent;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Event;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Timeline;
use App\Models\User;

describe('Right to Erasure — Erasure Page', function () {
    it('shows erasure index for authenticated user', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('erasure.index'))
            ->assertOk()
            ->assertSee('Hak Penghapusan Data')
            ->assertSee('Ringkasan Data Anda');
    });

    it('prevents unauthenticated access to erasure page', function () {
        $this->get(route('erasure.index'))
            ->assertRedirect(route('login'));
    });

    it('shows child data summary on erasure page', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        Timeline::factory()->create(['child_id' => $child->id]);
        Diary::factory()->create(['child_id' => $child->id]);

        $this->actingAs($user)
            ->get(route('erasure.index'))
            ->assertOk()
            ->assertSee($child->name);
    });

    it('shows warning banner about permanent deletion', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('erasure.index'))
            ->assertOk()
            ->assertSee('Peringatan Penting')
            ->assertSee('permanen');
    });

    it('shows delete account section', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('erasure.index'))
            ->assertOk()
            ->assertSee('Hapus Akun dan Semua Data')
            ->assertSee('HAPUS AKUN SAYA');
    });
});

describe('Right to Erasure — Delete Child', function () {
    it('deletes child data with correct password', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $child = Child::factory()->create(['user_id' => $user->id]);
        Timeline::factory()->create(['child_id' => $child->id]);
        Diary::factory()->create(['child_id' => $child->id]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), [
                'password' => 'password123',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('children', ['id' => $child->id]);
    });

    it('prevents deletion with wrong password', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), [
                'password' => 'wrongpassword',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('children', ['id' => $child->id]);
    });

    it('prevents deleting other users child', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $otherUser = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), [
                'password' => 'password123',
            ])
            ->assertForbidden();
    });

    it('requires password for child deletion', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), [])
            ->assertSessionHasErrors('password');
    });

    it('deletes related data when deleting child', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id]);
        $diary = Diary::factory()->create(['child_id' => $child->id]);
        $growth = Growth::factory()->create(['child_id' => $child->id]);
        $health = HealthRecord::factory()->create(['child_id' => $child->id]);
        $event = Event::factory()->create(['child_id' => $child->id]);
        $document = Document::factory()->create(['child_id' => $child->id]);
        Consent::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'consent_type' => 'data_collection',
            'granted' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), [
                'password' => 'password123',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('children', ['id' => $child->id]);
        $this->assertDatabaseCount('timelines', 0);
        $this->assertDatabaseCount('diaries', 0);
        $this->assertDatabaseCount('growths', 0);
        $this->assertDatabaseCount('health_records', 0);
        $this->assertDatabaseCount('events', 0);
        $this->assertDatabaseCount('documents', 0);
        $this->assertDatabaseCount('consents', 0);
    });

    it('creates audit log when deleting child', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $child = Child::factory()->create(['user_id' => $user->id, 'name' => 'Audit Test Child']);

        $this->actingAs($user)
            ->delete(route('erasure.destroyChild', $child), [
                'password' => 'password123',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'erasure.child_deleted',
            'user_id' => $user->id,
        ]);
    });
});

describe('Right to Erasure — Delete Account', function () {
    it('deletes entire account with correct password and confirmation', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyAccount'), [
                'password' => 'password123',
                'confirmation' => 'HAPUS AKUN SAYA',
            ])
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });

    it('prevents account deletion with wrong password', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyAccount'), [
                'password' => 'wrongpassword',
                'confirmation' => 'HAPUS AKUN SAYA',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    });

    it('prevents account deletion without confirmation text', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyAccount'), [
                'password' => 'password123',
                'confirmation' => 'wrong text',
            ])
            ->assertSessionHasErrors('confirmation');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    });

    it('requires both password and confirmation', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('erasure.destroyAccount'), [])
            ->assertSessionHasErrors(['password', 'confirmation']);
    });

    it('logs out user after account deletion', function () {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->actingAs($user)
            ->delete(route('erasure.destroyAccount'), [
                'password' => 'password123',
                'confirmation' => 'HAPUS AKUN SAYA',
            ])
            ->assertRedirect(route('login'));
    });
});
