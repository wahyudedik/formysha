<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $event_date
 * @property string|null $mood
 * @property array|null $tags
 * @property \Illuminate\Database\Eloquent\Collection|null $media
 * @property \Carbon\Carbon $created_at
 */
class TimelineResource extends JsonResource
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
            'mood' => $this->mood,
            'tags' => $this->tags,
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at,
        ];
    }
}
