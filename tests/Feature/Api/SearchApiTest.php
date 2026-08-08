<?php

use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;

describe('Search API', function () {
    it('can search and return results', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Timeline::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'title' => 'Cerita Pertama',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/search?q=Pertama');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'timelines',
                ],
            ]);
    });

    it('can search with type filter', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Timeline::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'title' => 'Cerita Timeline',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/search?q=Timeline&type=timeline');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'timelines',
                ],
            ]);
    });
});
