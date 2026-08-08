<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property int $price_monthly
 * @property int|null $price_yearly
 * @property int $max_children
 * @property int $max_photos
 * @property int $max_videos
 * @property int $max_storage_mb
 * @property array|null $features
 * @property bool $is_active
 * @property Carbon $created_at
 */
class PlanResource extends JsonResource
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
            'price_monthly' => $this->price_monthly,
            'price_yearly' => $this->price_yearly,
            'max_children' => $this->max_children,
            'max_photos' => $this->max_photos,
            'max_videos' => $this->max_videos,
            'max_storage_mb' => $this->max_storage_mb,
            'features' => $this->features,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
