<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Growth;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Growth>
 */
class GrowthFactory extends Factory
{
    protected $model = Growth::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'measured_at' => fake()->dateTimeThisYear()->format('Y-m-d'),
            'weight_kg' => fake()->randomFloat(1, 3.0, 40.0),
            'height_cm' => fake()->randomFloat(1, 45.0, 120.0),
            'head_circumference_cm' => fake()->optional(0.7)->randomFloat(1, 30.0, 55.0),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Set a specific measured date.
     */
    public function atDate(string $date): static
    {
        return $this->state(fn () => ['measured_at' => $date]);
    }

    /**
     * Set a specific weight.
     */
    public function withWeight(float $weight): static
    {
        return $this->state(fn () => ['weight_kg' => $weight]);
    }

    /**
     * Set a specific height.
     */
    public function withHeight(float $height): static
    {
        return $this->state(fn () => ['height_cm' => $height]);
    }

    /**
     * Set head circumference.
     */
    public function withHeadCircumference(float $cm): static
    {
        return $this->state(fn () => ['head_circumference_cm' => $cm]);
    }
}
