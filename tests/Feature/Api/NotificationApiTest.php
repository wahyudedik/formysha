<?php

use App\Models\Notification;
use App\Models\User;

describe('Notification API', function () {
    it('can list notifications', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Notification::factory()->create(['user_id' => $user->id, 'title' => 'Test Notification']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/notifications');

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Test Notification']);
    });

    it('can mark notification as read', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $notification = Notification::factory()->unread()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/notifications/'.$notification->id.'/read');

        $response->assertOk();

        $notification->refresh();
        expect($notification->is_read)->toBeTrue();
    });

    it('can mark all notifications as read', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Notification::factory()->unread()->create(['user_id' => $user->id]);
        Notification::factory()->unread()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/notifications/read-all');

        $response->assertOk();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        expect($unreadCount)->toBe(0);
    });

    it('can get unread count', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Notification::factory()->unread()->create(['user_id' => $user->id]);
        Notification::factory()->unread()->create(['user_id' => $user->id]);
        Notification::factory()->read()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/notifications/unread-count');

        $response->assertOk()
            ->assertJsonPath('data.count', 2);
    });
});
