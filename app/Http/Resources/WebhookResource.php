<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $url
 * @property array $events
 * @property bool $is_active
 * @property Carbon|null $last_triggered_at
 * @property int $failure_count
 * @property Carbon $created_at
 */
class WebhookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'events' => $this->events,
            'is_active' => $this->is_active,
            'last_triggered_at' => $this->last_triggered_at,
            'failure_count' => $this->failure_count,
            'created_at' => $this->created_at,
        ];
    }
}
