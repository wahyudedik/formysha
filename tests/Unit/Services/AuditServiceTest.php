<?php

use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditService;

describe('AuditService', function () {
    it('creates audit log entry', function () {
        $service = app(AuditService::class);
        $user = User::factory()->create();

        $log = $service->log('user.login', null, $user, [], ['email' => $user->email]);

        expect($log)->not->toBeNull();
        expect($log->event)->toBe('user.login');
        expect($log->user_id)->toBe($user->id);
        expect($log->new_values)->toBe(['email' => $user->email]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'user.login',
            'user_id' => $user->id,
        ]);
    });

    it('returns logs for specific tenant', function () {
        $service = app(AuditService::class);

        $tenant = Tenant::create([
            'name' => 'Klinik Sehat',
            'slug' => 'klinik-sehat',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        // Create logs for this tenant
        $service->log('child.created', null, $user);
        $service->log('timeline.created', null, $user);

        // Create log for another tenant
        $otherTenant = Tenant::create([
            'name' => 'Daycare Lain',
            'slug' => 'daycare-lain',
            'is_active' => true,
        ]);
        $otherUser = User::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);
        $service->log('child.created', null, $otherUser);

        $logs = $service->getTenantLogs($tenant);

        expect($logs->count())->toBe(2);
        expect($logs->every(fn ($log) => $log->tenant_id === $tenant->id || $log->tenant_id === null))->toBeTrue();
    });
});
