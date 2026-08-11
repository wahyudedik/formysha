<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $child_id
 * @property string $tenant_id
 * @property string $status
 * @property string $permission
 * @property int|null $invited_by
 * @property Carbon|null $invited_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $expires_at
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ConnectionResource extends JsonResource
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
            'child_id' => $this->child_id,
            'tenant_id' => $this->tenant_id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'permission' => $this->permission->value,
            'permission_label' => $this->permission->label(),
            'permission_description' => $this->permission->description(),
            'invited_by' => $this->invited_by,
            'invited_at' => $this->invited_at,
            'accepted_at' => $this->accepted_at,
            'expires_at' => $this->expires_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'child' => new ChildResource($this->whenLoaded('child')),
            'tenant' => new TenantResource($this->whenLoaded('tenant')),
            'invitedBy' => new UserResource($this->whenLoaded('invitedBy')),
        ];
    }
}
