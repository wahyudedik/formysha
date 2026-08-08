<?php

namespace App\Services;

use App\Jobs\DeliverWebhookJob;
use App\Models\Tenant;
use App\Models\Webhook;

class WebhookService
{
    /**
     * Available webhook events.
     */
    public const AVAILABLE_EVENTS = [
        'tenant.created',
        'tenant.updated',
        'tenant.suspended',
        'tenant.activated',
        'subscription.created',
        'subscription.activated',
        'subscription.cancelled',
        'subscription.expired',
        'payment.approved',
        'payment.rejected',
        'child.created',
        'child.updated',
        'child.deleted',
        'timeline.created',
        'timeline.updated',
        'timeline.deleted',
        'album.created',
        'album.updated',
        'album.deleted',
        'diary.created',
        'diary.updated',
        'diary.deleted',
        'document.created',
        'document.updated',
        'document.deleted',
        'growth.created',
        'health_record.created',
        'event.created',
    ];

    /**
     * Register a new webhook for a tenant.
     */
    public function register(Tenant $tenant, string $url, array $events, string $secret): Webhook
    {
        return Webhook::create([
            'tenant_id' => $tenant->id,
            'url' => $url,
            'events' => $events,
            'secret' => $secret,
            'is_active' => true,
        ]);
    }

    /**
     * Unregister (delete) a webhook.
     */
    public function unregister(Webhook $webhook): bool
    {
        return $webhook->delete();
    }

    /**
     * Trigger a specific webhook with an event and payload.
     */
    public function trigger(Webhook $webhook, string $event, array $payload): void
    {
        if (! $webhook->is_active) {
            return;
        }

        if (! in_array($event, $webhook->events ?? [])) {
            return;
        }

        DeliverWebhookJob::dispatch($webhook, $event, $payload);
    }

    /**
     * Trigger all active webhooks for a tenant that listen to the given event.
     */
    public function triggerForTenant(Tenant $tenant, string $event, array $payload): void
    {
        $webhooks = Webhook::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->trigger($webhook, $event, $payload);
        }
    }

    /**
     * Get the list of available webhook events.
     */
    public function getAvailableEvents(): array
    {
        return self::AVAILABLE_EVENTS;
    }

    /**
     * Verify HMAC-SHA256 signature.
     */
    public function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
