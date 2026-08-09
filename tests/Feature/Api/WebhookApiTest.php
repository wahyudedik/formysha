<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\Webhook;
use App\Models\WebhookLog;

describe('Webhook API', function () {
    it('can list webhooks', function () {
        $tenant = Tenant::create([
            'name' => 'Webhook Tenant',
            'slug' => 'webhook-tenant',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        Webhook::create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/webhook',
            'secret' => str_repeat('a', 32),
            'events' => ['child.created'],
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/webhooks');

        $response->assertOk()
            ->assertJsonFragment(['url' => 'https://example.com/webhook']);
    });

    it('can create a webhook', function () {
        $tenant = Tenant::create([
            'name' => 'Webhook Tenant',
            'slug' => 'webhook-tenant-create',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/webhooks', [
                'url' => 'https://example.com/new-webhook',
                'events' => ['child.created', 'timeline.created'],
                'secret' => str_repeat('s', 32),
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.url', 'https://example.com/new-webhook');
    });

    it('can update a webhook', function () {
        $tenant = Tenant::create([
            'name' => 'Webhook Tenant',
            'slug' => 'webhook-tenant-update',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $webhook = Webhook::create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/old-webhook',
            'secret' => str_repeat('a', 32),
            'events' => ['child.created'],
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/webhooks/'.$webhook->id, [
                'url' => 'https://example.com/updated-webhook',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.url', 'https://example.com/updated-webhook');
    });

    it('can delete a webhook', function () {
        $tenant = Tenant::create([
            'name' => 'Webhook Tenant',
            'slug' => 'webhook-tenant-delete',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $webhook = Webhook::create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/delete-webhook',
            'secret' => str_repeat('a', 32),
            'events' => ['child.created'],
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/webhooks/'.$webhook->id);

        $response->assertOk();

        $this->assertDatabaseMissing('webhooks', [
            'id' => $webhook->id,
        ]);
    });

    it('can view webhook logs', function () {
        $tenant = Tenant::create([
            'name' => 'Webhook Tenant',
            'slug' => 'webhook-tenant-logs',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $webhook = Webhook::create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/logs-webhook',
            'secret' => str_repeat('a', 32),
            'events' => ['child.created'],
            'is_active' => true,
        ]);

        WebhookLog::create([
            'webhook_id' => $webhook->id,
            'event' => 'child.created',
            'payload' => '{"test": true}',
            'response_code' => 200,
            'success' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/webhooks/'.$webhook->id.'/logs');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    });
});
