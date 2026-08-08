<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int|float $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload,
    ) {
        $this->onQueue('webhooks');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payloadJson = json_encode([
            'event' => $this->event,
            'data' => $this->payload,
            'timestamp' => now()->toIso8601String(),
        ], JSON_THROW_ON_ERROR);

        $timestamp = now()->toIso8601String();
        $signature = hash_hmac('sha256', $payloadJson, $this->webhook->secret);

        $headers = [
            'Content-Type' => 'application/json',
            'X-Webhook-Event' => $this->event,
            'X-Webhook-Signature' => $signature,
            'X-Webhook-Timestamp' => $timestamp,
            'Accept' => 'application/json',
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post($this->webhook->url, json_decode($payloadJson, true));

            $success = $response->successful();

            WebhookLog::create([
                'webhook_id' => $this->webhook->id,
                'event' => $this->event,
                'payload' => $payloadJson,
                'response_code' => $response->status(),
                'response_body' => $response->body(),
                'success' => $success,
                'delivered_at' => $success ? now() : null,
            ]);

            if ($success) {
                $this->webhook->update([
                    'last_triggered_at' => now(),
                    'failure_count' => 0,
                ]);
            } else {
                $this->webhook->increment('failure_count');
            }
        } catch (\Exception $e) {
            WebhookLog::create([
                'webhook_id' => $this->webhook->id,
                'event' => $this->event,
                'payload' => $payloadJson,
                'response_code' => null,
                'response_body' => $e->getMessage(),
                'success' => false,
            ]);

            $this->webhook->increment('failure_count');

            Log::error('Webhook delivery failed', [
                'webhook_id' => $this->webhook->id,
                'event' => $this->event,
                'url' => $this->webhook->url,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
