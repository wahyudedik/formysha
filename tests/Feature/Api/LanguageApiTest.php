<?php

use App\Models\User;

describe('Language API', function () {
    it('can switch language via API', function () {
        $user = User::factory()->create(['language' => 'id']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/me', [
                'language' => 'en',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.user.language', 'en');

        $user->refresh();
        expect($user->language)->toBe('en');
    });

    it('can set language to Indonesian', function () {
        $user = User::factory()->create(['language' => 'en']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/me', [
                'language' => 'id',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.user.language', 'id');
    });

    it('rejects invalid language code', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/me', [
                'language' => 'fr',
            ]);

        $response->assertUnprocessable();
    });

    it('defaults language to Indonesian', function () {
        $user = User::factory()->create(['language' => 'id']);

        expect($user->language)->toBe('id');
    });

    it('can get current profile with language', function () {
        $user = User::factory()->create(['language' => 'en']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('data.user.language', 'en');
    });
});
