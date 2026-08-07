<?php

use App\Models\Child;
use App\Models\User;

describe('Child Management', function () {
    it('redirects unauthenticated users to login', function () {
        $this->get(route('children.index'))->assertRedirect(route('login'));
    });

    it('shows children index page for authenticated users', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('children.index'))
            ->assertOk()
            ->assertSee('Anak Saya');
    });

    it('shows empty state when no children exist', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('children.index'))
            ->assertOk()
            ->assertSee('Belum Ada Profil Anak');
    });

    it('displays children list', function () {
        $user = User::factory()->create();
        Child::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mysha Aisyah',
        ]);

        $this->actingAs($user)
            ->get(route('children.index'))
            ->assertOk()
            ->assertSee('Mysha Aisyah');
    });

    it('shows create child form', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('children.create'))
            ->assertOk()
            ->assertSee('Tambah Anak Baru');
    });

    it('stores a new child successfully', function () {
        $user = User::factory()->create();

        $childData = [
            'name' => 'Qaireen Ahmad',
            'nickname' => 'Qai',
            'gender' => 'male',
            'date_of_birth' => '2025-01-20',
            'place_of_birth' => 'Bandung',
            'blood_type' => 'O',
            'bio' => 'Anak laki-laki yang ceria.',
        ];

        $this->actingAs($user)
            ->post(route('children.store'), $childData)
            ->assertRedirect();

        $this->assertDatabaseHas('children', [
            'user_id' => $user->id,
            'name' => 'Qaireen Ahmad',
            'slug' => 'qaireen-ahmad',
            'gender' => 'male',
        ]);
    });

    it('validates required fields when storing child', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('children.store'), [])
            ->assertSessionHasErrors(['name', 'gender', 'date_of_birth']);
    });

    it('shows child profile page', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'name' => 'Mysha Aisyah',
        ]);

        $this->actingAs($user)
            ->get(route('children.show', $child))
            ->assertOk()
            ->assertSee('Mysha Aisyah');
    });

    it('prevents viewing other users children', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get(route('children.show', $child))
            ->assertForbidden();
    });

    it('shows edit child form', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('children.edit', $child))
            ->assertOk()
            ->assertSee('Edit Profil Anak');
    });

    it('updates child successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Name',
        ]);

        $this->actingAs($user)
            ->put(route('children.update', $child), [
                'name' => 'New Name',
                'nickname' => 'NewNick',
                'gender' => 'female',
                'date_of_birth' => '2023-06-15',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('children', [
            'id' => $child->id,
            'name' => 'New Name',
            'nickname' => 'NewNick',
        ]);
    });

    it('deletes child successfully', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->delete(route('children.destroy', $child))
            ->assertRedirect(route('children.index'));

        $this->assertDatabaseMissing('children', ['id' => $child->id]);
    });

    it('generates slug automatically from name', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('children.store'), [
                'name' => 'Mysha Aisyah',
                'gender' => 'female',
                'date_of_birth' => '2023-06-15',
            ]);

        $this->assertDatabaseHas('children', [
            'user_id' => $user->id,
            'slug' => 'mysha-aisyah',
        ]);
    });
});
