<?php

namespace App\Listeners;

use App\Events\WebhookEvent;
use App\Services\WebhookService;

class DispatchWebhookListener
{
    /**
     * Create the listener instance.
     */
    public function __construct(
        private WebhookService $webhookService,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(WebhookEvent $event): void
    {
        $this->webhookService->triggerForTenant(
            $event->tenant,
            $event->eventName,
            $event->data
        );
    }
}
