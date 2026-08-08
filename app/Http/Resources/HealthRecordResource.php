<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string|null $doctor
 * @property string|null $hospital
 * @property string|null $notes
 * @property string|null $next_date
 * @property Carbon $created_at
 */
class HealthRecordResource extends JsonResource
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
            'type' => $this->type,
            'name' => $this->name,
            'doctor' => $this->doctor,
            'hospital' => $this->hospital,
            'notes' => $this->notes,
            'next_date' => $this->next_date,
            'created_at' => $this->created_at,
        ];
    }
}
