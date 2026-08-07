<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $file_path
 * @property string $file_type
 * @property int $file_size
 * @property string|null $alt_text
 * @property \Carbon\Carbon $created_at
 */
class MediaResource extends JsonResource
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
            'file_path' => $this->file_path,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'alt_text' => $this->alt_text,
            'created_at' => $this->created_at,
        ];
    }
}
