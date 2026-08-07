<?php

use App\Models\Child;
use App\Models\HealthRecord;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access health index', function () {
    $child = Child::factory()->create();

    $this->get(route('health.index', $child))
        ->assertRedirect(route('login'));
});

it('shows empty state when no health records exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('health.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Catatan Kesehatan');
});

it('lists health records for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    HealthRecord::factory()->immunization()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'BCG',
    ]);

    actingAs($user)
        ->get(route('health.index', $child))
        ->assertOk()
        ->assertSee('BCG')
        ->assertSee('Imunisasi');
});

it('filters health records by type', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    HealthRecord::factory()->immunization()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'BCG',
    ]);
    HealthRecord::factory()->illness()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Demam',
    ]);

    actingAs($user)
        ->get(route('health.index', ['child' => $child, 'type' => 'immunization']))
        ->assertOk()
        ->assertSee('BCG')
        ->assertDontSee('Demam');
});

it('prevents other users from viewing health records', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('health.index', $child))
        ->assertForbidden();
});

it('shows create health record form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('health.create', $child))
        ->assertOk()
        ->assertSee('Tambah Catatan Kesehatan');
});

it('stores a new health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('health.store', $child), [
            'type' => 'immunization',
            'name' => 'BCG',
            'date' => '2026-06-15',
            'doctor' => 'dr. Siti',
            'hospital' => 'RS Husada',
        ])
        ->assertRedirect(route('health.index', $child));

    assertDatabaseHas('health_records', [
        'child_id' => $child->id,
        'user_id' => $user->id,
        'type' => 'immunization',
        'name' => 'BCG',
    ]);
});

it('validates required fields when storing health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('health.store', $child), [
            'type' => '',
            'name' => '',
            'date' => '',
        ])
        ->assertSessionHasErrors(['type', 'name', 'date']);
});

it('validates type enum when storing health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('health.store', $child), [
            'type' => 'invalid_type',
            'name' => 'Test',
            'date' => '2026-06-15',
        ])
        ->assertSessionHasErrors('type');
});

it('prevents future date for health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('health.store', $child), [
            'type' => 'checkup',
            'name' => 'Pemeriksaan Rutin',
            'date' => now()->addDays(5)->format('Y-m-d'),
        ])
        ->assertSessionHasErrors('date');
});

it('allows future next_date for health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('health.store', $child), [
            'type' => 'immunization',
            'name' => 'Polio 3',
            'date' => '2026-06-15',
            'next_date' => now()->addMonths(1)->format('Y-m-d'),
        ])
        ->assertRedirect(route('health.index', $child));

    assertDatabaseHas('health_records', [
        'child_id' => $child->id,
        'name' => 'Polio 3',
    ]);
});

it('shows health record detail', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->immunization()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'BCG',
        'doctor' => 'dr. Siti',
    ]);

    actingAs($user)
        ->get(route('health.show', [$child, $record]))
        ->assertOk()
        ->assertSee('BCG')
        ->assertSee('dr. Siti')
        ->assertSee('Imunisasi');
});

it('shows edit health record form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->get(route('health.edit', [$child, $record]))
        ->assertOk()
        ->assertSee('Edit Catatan Kesehatan');
});

it('updates a health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->put(route('health.update', [$child, $record]), [
            'type' => 'medication',
            'name' => 'Parasetamol',
            'date' => $record->date->format('Y-m-d'),
            'doctor' => 'dr. Budi',
        ])
        ->assertRedirect(route('health.index', $child));

    assertDatabaseHas('health_records', [
        'id' => $record->id,
        'type' => 'medication',
        'name' => 'Parasetamol',
    ]);
});

it('deletes a health record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->delete(route('health.destroy', [$child, $record]))
        ->assertRedirect(route('health.index', $child));

    assertDatabaseMissing('health_records', ['id' => $record->id]);
});

it('prevents other users from viewing health record detail', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($otherUser)
        ->get(route('health.show', [$child, $record]))
        ->assertForbidden();
});

it('prevents other users from editing health records', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($otherUser)
        ->get(route('health.edit', [$child, $record]))
        ->assertForbidden();
});

it('prevents other users from deleting health records', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $record = HealthRecord::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($otherUser)
        ->delete(route('health.destroy', [$child, $record]))
        ->assertForbidden();
});

it('stores health record with minimal fields', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('health.store', $child), [
            'type' => 'other',
            'name' => 'Kontrol',
            'date' => '2026-06-15',
        ])
        ->assertRedirect(route('health.index', $child));

    assertDatabaseHas('health_records', [
        'child_id' => $child->id,
        'type' => 'other',
        'name' => 'Kontrol',
        'doctor' => null,
        'hospital' => null,
    ]);
});
