<?php

use App\Models\Child;
use App\Models\HealthRecord;
use App\Models\User;

describe('Health API', function () {
    it('can list health records', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        HealthRecord::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/children/'.$child->slug.'/health-records');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    });

    it('can create an immunization record', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/health-records', [
                'type' => 'immunization',
                'name' => 'BCG',
                'date' => '2025-02-01',
                'doctor' => 'Dr. Sari',
                'hospital' => 'RS Husada',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.type', 'immunization')
            ->assertJsonPath('data.name', 'BCG');
    });

    it('can create a disease record', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/children/'.$child->slug.'/health-records', [
                'type' => 'disease',
                'name' => 'Demam',
                'description' => 'Demam tinggi 39°C',
                'date' => '2025-05-10',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.type', 'disease')
            ->assertJsonPath('data.name', 'Demam');
    });
});
