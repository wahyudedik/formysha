<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $event_date
 * @property string $event_type
 * @property bool $is_recurring
 * @property \Carbon\Carbon $created_at
 */
class EventResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'event_type' => $this->event_type,
            'is_recurring' => $this->is_recurring,
            'created_at' => $this->created_at,
        ];
    }
}
