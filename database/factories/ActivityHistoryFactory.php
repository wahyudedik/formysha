<?php

namespace Database\Factories;

use App\Models\ActivityHistory;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityHistory>
 */
class ActivityHistoryFactory extends Factory
{
    protected $model = ActivityHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'connection_id' => Connection::factory(),
            'user_id' => User::factory(),
            'action' => 'connection.created',
            'entity_type' => null,
            'entity_id' => null,
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}
