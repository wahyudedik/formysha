<?php

use App\Models\Child;
use App\Models\Diary;
use App\Models\User;

describe('Diary API', function () {
    it('can list diaries', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'My Diary']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->id.'/diaries');

        $response->assertOk()
            ->assertJsonFragment(['title' => 'My Diary']);
    });

    it('can create a diary entry', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/children/'.$child->id.'/diaries', [
                'title' => 'Hari Pertama Sekolah',
                'content' => 'Hari ini adalah hari pertama sekolah.',
                'mood' => 'excited',
                'diary_date' => '2025-07-01',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Hari Pertama Sekolah');
    });

    it('can show diary detail', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'Test Diary']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/children/'.$child->id.'/diaries/'.$diary->id);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Test Diary');
    });

    it('can update a diary entry', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id, 'title' => 'Old Title']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/children/'.$child->id.'/diaries/'.$diary->id, [
                'title' => 'Updated Title',
                'content' => 'Updated content',
                'diary_date' => '2025-07-01',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    });

    it('can delete a diary entry', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $child = Child::factory()->create(['user_id' => $user->id]);
        $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/children/'.$child->id.'/diaries/'.$diary->id);

        $response->assertOk();

        $this->assertDatabaseMissing('diaries', [
            'id' => $diary->id,
        ]);
    });
});
