<?php

use App\Models\Child;
use App\Models\User;

describe('Child API', function () {
    it('can list children as empty', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children');

        $response->assertOk()
            ->assertJsonPath('data', []);
    });

    it('can list children', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children');

        $response->assertOk()
            ->assertJsonFragment(['name' => $child->name]);
    });

    it('can create a child', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/children', [
                'name' => 'Qaireen Ahmad',
                'gender' => 'male',
                'date_of_birth' => '2025-01-20',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Qaireen Ahmad');

        $this->assertDatabaseHas('children', [
            'user_id' => $user->id,
            'name' => 'Qaireen Ahmad',
        ]);
    });

    it('cannot create child without required fields', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/children', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'gender', 'date_of_birth']);
    });

    it('can show child detail', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id, 'name' => 'Mysha']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->slug);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Mysha');
    });

    it('can update a child', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/children/'.$child->slug, [
                'name' => 'New Name',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('children', [
            'id' => $child->id,
            'name' => 'New Name',
        ]);
    });

    it('can delete a child', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/children/'.$child->slug);

        $response->assertOk();

        $this->assertDatabaseMissing('children', [
            'id' => $child->id,
        ]);
    });

    it('cannot access other users child', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->slug);

        $response->assertForbidden();
    });
});
