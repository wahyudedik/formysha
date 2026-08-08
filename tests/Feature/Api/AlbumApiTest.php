<?php

use App\Models\Album;
use App\Models\Child;
use App\Models\User;

describe('Album API', function () {
    it('can list albums', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Album::factory()->create(['child_id' => $child->id, 'name' => 'My Album']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->id.'/albums');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'My Album']);
    });

    it('can create an album', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/children/'.$child->id.'/albums', [
                'name' => 'Album Keluarga',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Album Keluarga');
    });

    it('can show album with media', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $album = Album::factory()->create(['child_id' => $child->id, 'name' => 'Test Album']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->id.'/albums/'.$album->id);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Test Album');
    });

    it('can update an album', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $album = Album::factory()->create(['child_id' => $child->id, 'name' => 'Old Album']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/children/'.$child->id.'/albums/'.$album->id, [
                'name' => 'Updated Album',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Album');
    });

    it('can delete an album', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $album = Album::factory()->create(['child_id' => $child->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/children/'.$child->id.'/albums/'.$album->id);

        $response->assertOk();

        $this->assertDatabaseMissing('albums', [
            'id' => $album->id,
        ]);
    });
});
