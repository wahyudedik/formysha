<?php

use App\Models\Child;
use App\Models\Growth;
use App\Models\User;

describe('Growth API', function () {
    it('can list growth records', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Growth::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->id.'/growths');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    });

    it('can create a growth record', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/children/'.$child->id.'/growths', [
                'weight_kg' => 8.5,
                'height_cm' => 70.0,
                'measured_at' => '2025-06-15',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.weight_kg', '8.50')
            ->assertJsonPath('data.height_cm', '70.0');
    });

    it('can get chart data', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Growth::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'weight_kg' => 5.0, 'height_cm' => 55.0]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->id.'/growths/chart');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'labels',
                    'weight',
                    'height',
                    'head_circumference',
                ],
            ]);
    });
});
