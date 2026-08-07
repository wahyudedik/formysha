<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diary>
 */
class DiaryFactory extends Factory
{
    protected $model = Diary::class;

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
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(3),
            'mood' => fake()->randomElement(['happy', 'excited', 'calm', 'sad', 'surprised', 'loved']),
            'diary_date' => fake()->dateTimeThisYear()->format('Y-m-d'),
            'weather' => fake()->randomElement(['sunny', 'cloudy', 'rainy', 'windy', null]),
            'is_private' => true,
        ];
    }

    /**
     * Mark the diary as public.
     */
    public function public(): static
    {
        return $this->state(fn () => ['is_private' => false]);
    }

    /**
     * Mark the diary as private.
     */
    public function private(): static
    {
        return $this->state(fn () => ['is_private' => true]);
    }

    /**
     * Set a specific mood.
     */
    public function mood(string $mood): static
    {
        return $this->state(fn () => ['mood' => $mood]);
    }

    /**
     * Set a specific weather.
     */
    public function weather(string $weather): static
    {
        return $this->state(fn () => ['weather' => $weather]);
    }

    /**
     * Set a specific diary date.
     */
    public function forDate(string $date): static
    {
        return $this->state(fn () => ['diary_date' => $date]);
    }
}
