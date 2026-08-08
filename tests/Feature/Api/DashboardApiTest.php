<?php

use App\Models\Child;
use App\Models\User;

describe('Dashboard API', function () {
    it('can get dashboard data', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Child::factory()->create(['user_id' => $user->id, 'name' => 'Mysha']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/dashboard');

        $response->assertOk()
            ->assertJsonPath('data.children_count', 1)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'children_count',
                    'children',
                    'recent_timelines',
                    'upcoming_events',
                    'recent_diaries',
                ],
            ]);
    });
});
