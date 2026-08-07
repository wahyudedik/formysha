<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

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
            'description' => fake()->optional(0.5)->paragraph(),
            'event_date' => fake()->dateTimeThisYear('+1 year')->format('Y-m-d'),
            'event_time' => fake()->boolean(70) ? fake()->time('H:i') : null,
            'event_type' => fake()->randomElement([
                'birthday', 'immunization', 'appointment', 'school', 'other',
            ]),
            'is_recurring' => fake()->boolean(20),
            'recurrence_pattern' => fake()->optional(0.2)->randomElement([
                'weekly', 'monthly', 'yearly',
            ]),
            'reminder_at' => fake()->boolean(40) ? fake()->dateTimeThisYear('+1 year') : null,
        ];
    }

    /**
     * Mark the event as a birthday.
     */
    public function birthday(): static
    {
        return $this->state(fn () => ['event_type' => 'birthday']);
    }

    /**
     * Mark the event as an immunization.
     */
    public function immunization(): static
    {
        return $this->state(fn () => ['event_type' => 'immunization']);
    }

    /**
     * Mark the event as an appointment.
     */
    public function appointment(): static
    {
        return $this->state(fn () => ['event_type' => 'appointment']);
    }

    /**
     * Mark the event as a school event.
     */
    public function school(): static
    {
        return $this->state(fn () => ['event_type' => 'school']);
    }

    /**
     * Set a specific event date.
     */
    public function forDate(string $date): static
    {
        return $this->state(fn () => ['event_date' => $date]);
    }
}
