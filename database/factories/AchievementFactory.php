<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(array_keys(Achievement::TYPES));
        $meta = Achievement::TYPES[$type];

        return [
            'user_id' => User::factory(),
            'child_id' => Child::factory(),
            'type' => $type,
            'name' => $meta['name'],
            'description' => $meta['description'],
            'icon' => $meta['icon'],
            'earned_at' => null,
        ];
    }

    /**
     * Indicate that the achievement is earned.
     */
    public function earned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'earned_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate a specific type.
     */
    public function ofType(string $type): static
    {
        $meta = Achievement::TYPES[$type] ?? [];

        return $this->state(fn (array $attributes): array => [
            'type' => $type,
            'name' => $meta['name'] ?? $type,
            'description' => $meta['description'] ?? null,
            'icon' => $meta['icon'] ?? '🏆',
        ]);
    }
}
