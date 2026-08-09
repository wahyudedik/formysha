<?php

use App\Mail\SubscriptionMail;
use App\Mail\WelcomeMail;
use App\Models\Child;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;

describe('NotificationService', function () {
    beforeEach(function () {
        $this->service = new NotificationService;
        Mail::fake();
    });

    it('can send welcome notification and email', function () {
        $user = User::factory()->create();

        $notification = $this->service->sendWelcome($user);

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Selamat Datang di ForMysha! 🎉');
        expect($notification->type)->toBe('success');
        expect($notification->user_id)->toBe($user->id);
        expect($notification->is_read)->toBeFalse();

        Mail::assertQueued(WelcomeMail::class, 1);
    });

    it('can send immunization reminder notification', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $notification = $this->service->sendImmunizationReminder(
            $user,
            $child,
            'DPT',
            '15 Januari 2026',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Pengingat Imunisasi 💉');
        expect($notification->type)->toBe('reminder');
        expect($notification->child_id)->toBe($child->id);
        expect($notification->message)->toContain('DPT');
        expect($notification->message)->toContain($child->name);
    });

    it('can send growth milestone notification', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $notification = $this->service->sendGrowthMilestone(
            $user,
            $child,
            'Berat badan mencapai 10 kg',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Pencapaian Pertumbuhan! 🌟');
        expect($notification->type)->toBe('success');
        expect($notification->message)->toContain('10 kg');
    });

    it('can send subscription update notification and email', function () {
        $user = User::factory()->create();

        $notification = $this->service->sendSubscriptionUpdate(
            $user,
            'Premium',
            'active',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Update Langganan 📋');
        expect($notification->type)->toBe('info');
        expect($notification->message)->toContain('Premium');
        expect($notification->message)->toContain('active');

        Mail::assertQueued(SubscriptionMail::class, 1);
    });

    it('can send birthday reminder notification', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $notification = $this->service->sendBirthdayReminder(
            $user,
            $child,
            '15 Maret 2026',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Pengingat Ulang Tahun! 🎂');
        expect($notification->type)->toBe('reminder');
    });

    it('can send document reminder notification', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $notification = $this->service->sendDocumentReminder(
            $user,
            $child,
            'Akta Lahir',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Pengingat Dokumen 📄');
        expect($notification->type)->toBe('warning');
        expect($notification->message)->toContain('Akta Lahir');
    });

    it('can create in-app notification with all fields', function () {
        $user = User::factory()->create();
        $child = Child::factory()->create(['user_id' => $user->id]);

        $notification = $this->service->createInApp(
            user: $user,
            title: 'Test Notification',
            message: 'This is a test.',
            type: 'info',
            icon: '🔔',
            child: $child,
            actionUrl: '/dashboard',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->title)->toBe('Test Notification');
        expect($notification->message)->toBe('This is a test.');
        expect($notification->type)->toBe('info');
        expect($notification->icon)->toBe('🔔');
        expect($notification->child_id)->toBe($child->id);
        expect($notification->action_url)->toBe('/dashboard');
    });

    it('can create in-app notification with minimal fields', function () {
        $user = User::factory()->create();

        $notification = $this->service->createInApp(
            user: $user,
            title: 'Simple',
            message: 'Hello',
        );

        expect($notification)->toBeInstanceOf(Notification::class);
        expect($notification->child_id)->toBeNull();
        expect($notification->icon)->toBeNull();
        expect($notification->action_url)->toBeNull();
    });
});
