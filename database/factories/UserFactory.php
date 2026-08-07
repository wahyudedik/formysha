<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'avatar' => null,
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'date_of_birth' => fake()->optional(0.6)->dateTimeBetween('-40 years', '-20 years'),
            'address' => fake()->optional(0.5)->address(),
            'role' => 'parent',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a guardian.
     */
    public function guardian(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => 'guardian',
        ]);
    }

    /**
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => 'super_admin',
        ]);
    }

    /**
     * Indicate that the user is a tenant admin.
     */
    public function tenantAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => 'tenant_admin',
        ]);
    }
}
