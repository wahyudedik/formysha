<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $mood
 * @property string $diary_date
 * @property \Carbon\Carbon $created_at
 */
class DiaryResource extends JsonResource
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
            'content' => $this->content,
            'mood' => $this->mood,
            'diary_date' => $this->diary_date,
            'created_at' => $this->created_at,
        ];
    }
}
