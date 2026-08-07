<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $gender
 * @property string $date_of_birth
 * @property string|null $photo
 * @property bool $is_public
 * @property \Carbon\Carbon $created_at
 */
class ChildResource extends JsonResource
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
            'slug' => $this->slug,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'photo' => $this->photo,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
        ];
    }
}
