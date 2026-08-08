<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property float|null $weight_kg
 * @property float|null $height_cm
 * @property float|null $head_circumference_cm
 * @property string $recorded_at
 * @property Carbon $created_at
 */
class GrowthResource extends JsonResource
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
            'weight_kg' => $this->weight_kg,
            'height_cm' => $this->height_cm,
            'head_circumference_cm' => $this->head_circumference_cm,
            'recorded_at' => $this->measured_at,
            'created_at' => $this->created_at,
        ];
    }
}
