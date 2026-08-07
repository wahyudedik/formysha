<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Timeline>
 */
class TimelineFactory extends Factory
{
    protected $model = Timeline::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $moods = ['happy', 'excited', 'calm', 'sad', 'surprised', 'loved'];

        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(2),
            'event_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'event_time' => fake()->optional(0.5)->time('H:i'),
            'location' => fake()->optional(0.6)->city(),
            'latitude' => fake()->optional(0.4)->latitude(-8, -6),
            'longitude' => fake()->optional(0.4)->longitude(106, 115),
            'mood' => fake()->optional(0.7)->randomElement($moods),
            'tags' => fake()->optional(0.5)->randomElements(['first', 'milestone', 'family', 'fun', 'growth'], random_int(1, 3)),
            'is_featured' => fake()->boolean(15),
        ];
    }

    /**
     * Mark the timeline as featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Set a specific mood.
     */
    public function mood(string $mood): static
    {
        return $this->state(fn (array $attributes) => [
            'mood' => $mood,
        ]);
    }

    /**
     * Set a specific location.
     */
    public function atLocation(string $location): static
    {
        return $this->state(fn (array $attributes) => [
            'location' => $location,
        ]);
    }

    /**
     * Mark with specific tags.
     */
    public function withTags(array $tags): static
    {
        return $this->state(fn (array $attributes) => [
            'tags' => $tags,
        ]);
    }
}
