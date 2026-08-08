<?php

use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;

describe('Timeline API', function () {
    it('can list timelines for a child', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'First Steps']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->slug.'/timelines');

        $response->assertOk()
            ->assertJsonFragment(['title' => 'First Steps']);
    });

    it('can create a timeline entry', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/children/'.$child->slug.'/timelines', [
                'title' => 'First Smile',
                'description' => 'Anak tersenyum pertama kali',
                'event_date' => '2025-03-15',
                'mood' => 'happy',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'First Smile');
    });

    it('can show timeline detail', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'Test Timeline']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->slug.'/timelines/'.$timeline->id);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Test Timeline');
    });

    it('can update a timeline entry', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'Old Title']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/children/'.$child->slug.'/timelines/'.$timeline->id, [
                'title' => 'New Title',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'New Title');
    });

    it('can delete a timeline entry', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/children/'.$child->slug.'/timelines/'.$timeline->id);

        $response->assertOk();

        $this->assertDatabaseMissing('timelines', [
            'id' => $timeline->id,
        ]);
    });
});
