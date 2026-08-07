<?php

use App\Models\Child;
use App\Models\Notification;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe('Notification Module', function () {
    it('requires authentication to access notifications', function () {
        $this->get(route('notifications.index'))
            ->assertRedirect('/login');
    });

    it('shows empty state when no notifications exist', function () {
        $user = User::factory()->create();
        actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Belum Ada Notifikasi');
    });

    it('displays notifications list', function () {
        $user = User::factory()->create();
        Notification::factory()->count(3)->create(['user_id' => $user->id]);

        actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('3 notifikasi');
    });

    it('shows unread count in notifications list', function () {
        $user = User::factory()->create();
        Notification::factory()->unread()->count(2)->create(['user_id' => $user->id]);
        Notification::factory()->read()->count(3)->create(['user_id' => $user->id]);

        actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Belum dibaca: 2');
    });

    it('does not show other users notifications', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Notification::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Notifikasi Pengguna Lain',
        ]);

        actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertDontSee('Notifikasi Pengguna Lain');
    });

    it('can mark notification as read', function () {
        $user = User::factory()->create();
        $notification = Notification::factory()->unread()->create(['user_id' => $user->id]);

        actingAs($user)->post(route('notifications.markAsRead', $notification))
            ->assertRedirect(route('notifications.index'));

        $notification->refresh();
        expect($notification->is_read)->toBeTrue();
        expect($notification->read_at)->not->toBeNull();
    });

    it('redirects to action_url when marking as read', function () {
        $user = User::factory()->create();
        $notification = Notification::factory()->unread()
            ->withActionUrl('/children/1/growth')
            ->create(['user_id' => $user->id]);

        actingAs($user)->post(route('notifications.markAsRead', $notification))
            ->assertRedirect('/children/1/growth');
    });

    it('prevents marking other users notification as read', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $otherUser->id]);

        actingAs($user)->post(route('notifications.markAsRead', $notification))
            ->assertStatus(403);
    });

    it('can mark all notifications as read', function () {
        $user = User::factory()->create();
        Notification::factory()->unread()->count(5)->create(['user_id' => $user->id]);

        actingAs($user)->post(route('notifications.markAllRead'))
            ->assertRedirect(route('notifications.index'));

        expect(Notification::where('user_id', $user->id)->where('is_read', false)->count())->toBe(0);
        expect(Notification::where('user_id', $user->id)->where('is_read', true)->count())->toBe(5);
    });

    it('can delete a notification', function () {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        actingAs($user)->delete(route('notifications.destroy', $notification))
            ->assertRedirect(route('notifications.index'));

        expect(Notification::find($notification->id))->toBeNull();
    });

    it('prevents deleting other users notification', function () {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $otherUser->id]);

        actingAs($user)->delete(route('notifications.destroy', $notification))
            ->assertStatus(403);
    });

    it('can display notification with child reference', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);
        Notification::factory()->forChild($child)->create(['user_id' => $user->id]);

        actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee($child->name);
    });

    it('notification badge returns correct unread count', function () {
        $user = User::factory()->create();
        Notification::factory()->unread()->count(3)->create(['user_id' => $user->id]);
        Notification::factory()->read()->count(2)->create(['user_id' => $user->id]);

        expect($user->unread_notifications_count)->toBe(3);
    });

    it('creates notification with correct type labels', function () {
        $reminder = Notification::factory()->ofType('reminder')->create();
        $info = Notification::factory()->ofType('info')->create();
        $warning = Notification::factory()->ofType('warning')->create();
        $success = Notification::factory()->ofType('success')->create();

        expect($reminder->type_label)->toBe('Pengingat');
        expect($info->type_label)->toBe('Informasi');
        expect($warning->type_label)->toBe('Peringatan');
        expect($success->type_label)->toBe('Berhasil');
    });
});
