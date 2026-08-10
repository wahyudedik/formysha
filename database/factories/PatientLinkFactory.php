<?php

namespace Database\Factories;

use App\Enums\PatientLinkStatus;
use App\Models\Child;
use App\Models\PatientLink;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PatientLink>
 */
class PatientLinkFactory extends Factory
{
    protected $model = PatientLink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facility_tenant_id' => Tenant::factory(),
            'parent_user_id' => User::factory(),
            'child_id' => Child::factory(),
            'link_code' => strtoupper(Str::random(8)),
            'status' => PatientLinkStatus::Pending,
            'permissions' => ['view_timeline', 'view_growth', 'view_health'],
            'linked_at' => null,
            'revoked_at' => null,
        ];
    }

    /**
     * Active patient link.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PatientLinkStatus::Active,
            'linked_at' => now(),
        ]);
    }

    /**
     * Pending patient link.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PatientLinkStatus::Pending,
        ]);
    }

    /**
     * Revoked patient link.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PatientLinkStatus::Revoked,
            'revoked_at' => now(),
        ]);
    }
}
