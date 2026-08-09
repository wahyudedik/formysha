<?php

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

describe('Family Member Management', function () {
    it('shows family members index for child owner', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('family.index', $child))
            ->assertOk()
            ->assertSee('Keluarga');
    });

    it('prevents viewing other users family members', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->get(route('family.index', $child))
            ->assertForbidden();
    });

    it('shows create family member form', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('family.create', $child))
            ->assertOk()
            ->assertSee('Tambah Anggota Keluarga');
    });

    it('stores a new family member successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $memberData = [
            'name' => 'Rina Sari',
            'relationship' => 'mother',
            'phone' => '08123456789',
            'email' => 'rina@email.com',
        ];

        $this->actingAs($user)
            ->post(route('family.store', $child), $memberData)
            ->assertRedirect(route('family.index', $child));

        $this->assertDatabaseHas('family_members', [
            'child_id' => $child->id,
            'name' => 'Rina Sari',
            'relationship' => 'mother',
        ]);
    });

    it('validates required fields when storing family member', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('family.store', $child), [])
            ->assertSessionHasErrors(['name', 'relationship']);
    });

    it('shows edit family member form', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create(['child_id' => $child->id]);

        $this->actingAs($user)
            ->get(route('family.edit', [$child, $member]))
            ->assertOk()
            ->assertSee('Edit');
    });

    it('updates family member successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create([
            'child_id' => $child->id,
            'name' => 'Old Name',
        ]);

        $this->actingAs($user)
            ->put(route('family.update', [$child, $member]), [
                'name' => 'New Name',
                'relationship' => 'father',
            ])
            ->assertRedirect(route('family.index', $child));

        $this->assertDatabaseHas('family_members', [
            'id' => $member->id,
            'name' => 'New Name',
            'relationship' => 'father',
        ]);
    });

    it('deletes family member successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create(['child_id' => $child->id]);

        $this->actingAs($user)
            ->delete(route('family.destroy', [$child, $member]))
            ->assertRedirect(route('family.index', $child));

        $this->assertDatabaseMissing('family_members', ['id' => $member->id]);
    });

    it('prevents deleting other users family members', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $otherUser->id]);
        $member = FamilyMember::factory()->create(['child_id' => $child->id]);

        $this->actingAs($user)
            ->delete(route('family.destroy', [$child, $member]))
            ->assertForbidden();
    });

    it('sets tenant_id when creating family member with active tenant', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Tenant',
            'slug' => 'keluarga-tenant-'.Str::random(5),
        ]);
        $plan = Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'price_monthly' => 50000,
            'max_children' => 5,
            'max_family_members' => 10,
            'is_active' => true,
        ]);
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        $this->actingAs($user)
            ->post(route('family.store', $child), [
                'name' => 'Ayah Tenant',
                'relationship' => 'father',
            ])
            ->assertRedirect(route('family.index', $child));

        $this->assertDatabaseHas('family_members', [
            'child_id' => $child->id,
            'tenant_id' => $tenant->id,
            'name' => 'Ayah Tenant',
        ]);
    });

    it('counts family members via tenant relationship', function () {
        $tenant = Tenant::create([
            'name' => 'Keluarga Count',
            'slug' => 'keluarga-count-'.Str::random(5),
        ]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
        ]);

        expect($tenant->familyMembers()->count())->toBe(0);

        FamilyMember::factory()->create(['child_id' => $child->id]);
        FamilyMember::factory()->create(['child_id' => $child->id]);

        expect($tenant->familyMembers()->count())->toBe(2);
    });
});
