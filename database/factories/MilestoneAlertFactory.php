<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\MilestoneAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MilestoneAlert>
 */
class MilestoneAlertFactory extends Factory
{
    protected $model = MilestoneAlert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(array_keys(MilestoneAlert::TYPES));
        $meta = MilestoneAlert::TYPES[$type];

        return [
            'user_id' => User::factory(),
            'child_id' => Child::factory(),
            'type' => $type,
            'title' => $meta['name'].' Test',
            'description' => fake()->sentence(),
            'icon' => $meta['icon'],
            'alert_date' => now()->toDateString(),
            'milestone_date' => now()->addDays(fake()->numberBetween(0, 7))->toDateString(),
            'is_dismissed' => false,
            'dismissed_at' => null,
        ];
    }

    /**
     * Mark the alert as dismissed.
     */
    public function dismissed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_dismissed' => true,
            'dismissed_at' => now(),
        ]);
    }

    /**
     * Set a specific type.
     */
    public function ofType(string $type): static
    {
        $meta = MilestoneAlert::TYPES[$type] ?? ['name' => $type, 'icon' => '📌'];

        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'title' => $meta['name'].' Test',
            'icon' => $meta['icon'],
        ]);
    }
}
