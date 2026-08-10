<?php

namespace Database\Factories;

use App\Enums\ReferralStatus;
use App\Models\Child;
use App\Models\Referral;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Referral>
 */
class ReferralFactory extends Factory
{
    protected $model = Referral::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'child_id' => Child::factory(),
            'from_tenant_id' => Tenant::factory(),
            'to_tenant_id' => Tenant::factory(),
            'referring_staff_id' => User::factory(),
            'reason' => fake()->paragraph(1),
            'clinical_summary' => fake()->paragraph(2),
            'status' => ReferralStatus::Pending,
            'notes' => null,
        ];
    }

    /**
     * Pending referral.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReferralStatus::Pending,
        ]);
    }

    /**
     * Accepted referral.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReferralStatus::Accepted,
        ]);
    }

    /**
     * Completed referral.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReferralStatus::Completed,
        ]);
    }

    /**
     * Cancelled referral.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReferralStatus::Cancelled,
        ]);
    }
}
