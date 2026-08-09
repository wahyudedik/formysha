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

    it('can show a specific family member', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create([
            'child_id' => $child->id,
            'name' => 'Ibu Sari',
            'relationship' => 'mother',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/family-members/'.$member->id);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Ibu Sari')
            ->assertJsonPath('data.relationship', 'mother');
    });

    it('can update a family member', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create([
            'child_id' => $child->id,
            'name' => 'Old Name',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/children/'.$child->slug.'/family-members/'.$member->id, [
                'name' => 'New Name',
                'relationship' => 'father',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('family_members', [
            'id' => $member->id,
            'name' => 'New Name',
        ]);
    });

    it('can delete a family member', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create(['child_id' => $child->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/children/'.$child->slug.'/family-members/'.$member->id);

        $response->assertOk();

        $this->assertDatabaseMissing('family_members', ['id' => $member->id]);
    });

    it('prevents listing other users family members', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/family-members');

        $response->assertForbidden();
    });

    it('prevents creating family member for other users child', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/family-members', [
                'name' => 'Hacker',
                'relationship' => 'other',
            ]);

        $response->assertForbidden();
    });

    it('validates required fields when creating family member', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/family-members', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'relationship']);
    });

    it('returns relationship_label in response', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $member = FamilyMember::factory()->create([
            'child_id' => $child->id,
            'relationship' => 'father',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/family-members/'.$member->id);

        $response->assertOk()
            ->assertJsonPath('data.relationship_label', 'Ayah');
    });
});
