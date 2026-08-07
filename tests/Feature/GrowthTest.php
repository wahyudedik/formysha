<?php

use App\Models\Child;
use App\Models\Growth;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access growth index', function () {
    $child = Child::factory()->create();

    $this->get(route('growth.index', $child))
        ->assertRedirect(route('login'));
});

it('shows empty state when no growth records exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('growth.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Data Pertumbuhan');
});

it('lists growth records for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Growth::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'weight_kg' => 12.5,
        'height_cm' => 85.0,
    ]);

    actingAs($user)
        ->get(route('growth.index', $child))
        ->assertOk()
        ->assertSee('12,5')
        ->assertSee('85,0');
});

it('prevents other users from viewing growth records', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('growth.index', $child))
        ->assertForbidden();
});

it('shows create growth form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('growth.create', $child))
        ->assertOk()
        ->assertSee('Tambah Pengukuran');
});

it('stores a new growth record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => '2026-06-15',
            'weight_kg' => '12.5',
            'height_cm' => '85.0',
            'head_circumference_cm' => '45.0',
            'notes' => 'Pengukuran rutin',
        ])
        ->assertRedirect(route('growth.index', $child));

    assertDatabaseHas('growths', [
        'child_id' => $child->id,
        'user_id' => $user->id,
        'weight_kg' => 12.5,
        'height_cm' => 85.0,
    ]);
});

it('validates required fields when storing growth', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => '',
        ])
        ->assertSessionHasErrors('measured_at');
});

it('validates weight range when storing growth', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => '2026-06-15',
            'weight_kg' => '300',
        ])
        ->assertSessionHasErrors('weight_kg');
});

it('validates height range when storing growth', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => '2026-06-15',
            'height_cm' => '300',
        ])
        ->assertSessionHasErrors('height_cm');
});

it('shows edit growth form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $growth = Growth::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->get(route('growth.edit', [$child, $growth]))
        ->assertOk()
        ->assertSee('Edit Pengukuran');
});

it('updates a growth record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $growth = Growth::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'weight_kg' => 12.0,
    ]);

    actingAs($user)
        ->put(route('growth.update', [$child, $growth]), [
            'measured_at' => $growth->measured_at->format('Y-m-d'),
            'weight_kg' => '13.5',
            'height_cm' => '90.0',
        ])
        ->assertRedirect(route('growth.index', $child));

    assertDatabaseHas('growths', [
        'id' => $growth->id,
        'weight_kg' => 13.5,
        'height_cm' => 90.0,
    ]);
});

it('deletes a growth record', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $growth = Growth::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->delete(route('growth.destroy', [$child, $growth]))
        ->assertRedirect(route('growth.index', $child));

    assertDatabaseMissing('growths', ['id' => $growth->id]);
});

it('prevents other users from editing growth records', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $growth = Growth::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($otherUser)
        ->get(route('growth.edit', [$child, $growth]))
        ->assertForbidden();
});

it('prevents other users from deleting growth records', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $growth = Growth::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    actingAs($otherUser)
        ->delete(route('growth.destroy', [$child, $growth]))
        ->assertForbidden();
});

it('allows storing growth with only weight', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => '2026-06-15',
            'weight_kg' => '10.5',
        ])
        ->assertRedirect(route('growth.index', $child));

    assertDatabaseHas('growths', [
        'child_id' => $child->id,
        'weight_kg' => 10.5,
        'height_cm' => null,
    ]);
});

it('allows storing growth with only height', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => '2026-06-15',
            'height_cm' => '80.0',
        ])
        ->assertRedirect(route('growth.index', $child));

    assertDatabaseHas('growths', [
        'child_id' => $child->id,
        'weight_kg' => null,
        'height_cm' => 80.0,
    ]);
});

it('prevents future date for measured_at', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('growth.store', $child), [
            'measured_at' => now()->addDays(5)->format('Y-m-d'),
            'weight_kg' => '10.0',
        ])
        ->assertSessionHasErrors('measured_at');
});
