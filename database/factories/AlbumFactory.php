<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Child;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Album>
 */
class AlbumFactory extends Factory
{
    protected $model = Album::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'child_id' => Child::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->optional(0.6)->sentence(5),
            'cover_photo' => fake()->optional(0.5)->uuid().'.jpg',
            'is_private' => fake()->boolean(80),
            'sort_order' => 0,
        ];
    }

    /**
     * Make the album public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => false,
        ]);
    }

    /**
     * Make the album private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    /**
     * Set a cover photo.
     */
    public function withCover(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_photo' => fake()->uuid().'.jpg',
        ]);
    }
}
