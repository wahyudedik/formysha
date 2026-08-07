<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_private
 * @property int $sort_order
 * @property int|null $media_count
 * @property \Carbon\Carbon $created_at
 */
class AlbumResource extends JsonResource
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
            'name' => $this->name,
            'is_private' => $this->is_private,
            'sort_order' => $this->sort_order,
            'media_count' => $this->whenCounted('media'),
            'created_at' => $this->created_at,
        ];
    }
}
