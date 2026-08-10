<?php

namespace Database\Factories;

use App\Enums\ConsentType;
use App\Models\Child;
use App\Models\Consent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consent>
 */
class ConsentFactory extends Factory
{
    protected $model = Consent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'child_id' => Child::factory(),
            'consent_type' => fake()->randomElement(ConsentType::cases()),
            'granted' => true,
            'notes' => fake()->optional()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'revoked_at' => null,
        ];
    }

    public function granted(): static
    {
        return $this->state(fn () => [
            'granted' => true,
            'revoked_at' => null,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn () => [
            'granted' => true,
            'revoked_at' => now(),
        ]);
    }

    public function ofType(string $type): static
    {
        return $this->state(fn () => [
            'consent_type' => ConsentType::from($type),
        ]);
    }
}
