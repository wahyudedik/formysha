<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $file_path
 * @property bool $is_private
 * @property \Carbon\Carbon $created_at
 */
class DocumentResource extends JsonResource
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
            'type' => $this->type,
            'file_path' => $this->file_path,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
        ];
    }
}
