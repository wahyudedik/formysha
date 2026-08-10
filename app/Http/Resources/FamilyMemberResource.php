<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $relationship
 * @property string|null $phone
 * @property string|null $email
 * @property bool $is_primary
 * @property string $permission_level
 * @property Carbon $created_at
 */
class FamilyMemberResource extends JsonResource
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
            'relationship' => $this->relationship,
            'relationship_label' => $this->relationship_label,
            'phone' => $this->phone,
            'email' => $this->email,
            'photo' => $this->photo,
            'photo_url' => $this->photo ? asset('storage/'.$this->photo) : null,
            'is_primary' => $this->is_primary,
            'permission_level' => $this->permission_level?->value ?? 'view',
            'permission_label' => $this->permission_level?->label() ?? 'Lihat Saja',
            'created_at' => $this->created_at,
        ];
    }
}
