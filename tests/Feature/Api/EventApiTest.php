<?php

use App\Models\Child;
use App\Models\Event;
use App\Models\User;

describe('Event API', function () {
    it('can list events', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Event::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/events');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    });

    it('can create an event', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/events', [
                'title' => 'Imunisasi DPT',
                'event_date' => '2025-08-15',
                'event_type' => 'immunization',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Imunisasi DPT')
            ->assertJsonPath('data.event_type', 'immunization');
    });
});
