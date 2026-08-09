<?php

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\User;

describe('Family API', function () {
    it('can list family members', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        FamilyMember::factory()->create(['child_id' => $child->id, 'name' => 'Ayah']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/family-members');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Ayah']);
    });

    it('can create a family member', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/family-members', [
                'name' => 'Budi',
                'relationship' => 'father',
                'phone' => '08123456789',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Budi')
            ->assertJsonPath('data.relationship', 'father');
    });
});
