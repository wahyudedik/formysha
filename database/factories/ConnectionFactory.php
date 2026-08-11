<?php

namespace Database\Factories;

use App\Enums\ConnectionPermission;
use App\Enums\ConnectionStatus;
use App\Models\Child;
use App\Models\Connection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Connection>
 */
class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'child_id' => Child::factory(),
            'tenant_id' => fake()->uuid(),
            'status' => ConnectionStatus::Pending,
            'permission' => ConnectionPermission::View,
            'invited_by' => null,
            'invited_at' => now(),
            'accepted_at' => null,
            'expires_at' => null,
            'notes' => null,
            'metadata' => null,
        ];
    }

    /**
     * Active connection.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConnectionStatus::Active,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Pending connection.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConnectionStatus::Pending,
        ]);
    }

    /**
     * Referred connection.
     */
    public function referred(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConnectionStatus::Referred,
        ]);
    }

    /**
     * Connection with specific permission.
     */
    public function withPermission(ConnectionPermission $permission): static
    {
        return $this->state(function (array $attributes) use ($permission): array {
            return [
                'permission' => $permission,
            ];
        });
    }
}
