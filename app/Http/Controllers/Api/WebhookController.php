<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreWebhookRequest;
use App\Http\Requests\Api\UpdateWebhookRequest;
use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends ApiController
{
    public function __construct(
        private WebhookService $webhookService,
    ) {}

    /**
     * List all webhooks for the current tenant.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);

        $webhooks = Webhook::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($webhooks, 'Daftar webhook berhasil diambil');
    }

    /**
     * Create a new webhook.
     */
    public function store(StoreWebhookRequest $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validated();

        $webhook = $this->webhookService->register(
            $tenant,
            $validated['url'],
            $validated['events'],
            $validated['secret']
        );

        return $this->successResponse($webhook, 'Webhook berhasil dibuat', 201);
    }

    /**
     * Show a specific webhook.
     */
    public function show(Request $request, Webhook $webhook): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);
        abort_if($webhook->tenant_id !== $tenant->id, 403);

        return $this->successResponse($webhook, 'Detail webhook berhasil diambil');
    }

    /**
     * Update a specific webhook.
     */
    public function update(UpdateWebhookRequest $request, Webhook $webhook): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);
        abort_if($webhook->tenant_id !== $tenant->id, 403);

        $validated = $request->validated();

        $webhook->update(array_filter($validated));

        return $this->successResponse($webhook->fresh(), 'Webhook berhasil diperbarui');
    }

    /**
     * Delete a specific webhook.
     */
    public function destroy(Request $request, Webhook $webhook): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);
        abort_if($webhook->tenant_id !== $tenant->id, 403);

        $this->webhookService->unregister($webhook);

        return $this->successResponse(null, 'Webhook berhasil dihapus');
    }

    /**
     * Send a test webhook.
     */
    public function test(Request $request, Webhook $webhook): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);
        abort_if($webhook->tenant_id !== $tenant->id, 403);

        $this->webhookService->trigger($webhook, 'webhook.test', [
            'message' => 'Ini adalah webhook test dari ForMysha.',
            'timestamp' => now()->toIso8601String(),
        ]);

        return $this->successResponse(null, 'Webhook test berhasil dikirim');
    }

    /**
     * Show webhook delivery logs.
     */
    public function logs(Request $request, Webhook $webhook): JsonResponse
    {
        $tenant = $request->user()->tenant;

        abort_unless($tenant, 404);
        abort_if($webhook->tenant_id !== $tenant->id, 403);

        $logs = $webhook->logs()
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($logs, 'Log webhook berhasil diambil');
    }
}
