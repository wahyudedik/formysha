<?php

namespace Database\Factories;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tenant_id' => Tenant::factory(),
            'staff_role' => StaffRole::Staff,
            'specialization' => null,
            'license_number' => null,
            'is_active' => true,
        ];
    }

    /**
     * Staff with doctor role.
     */
    public function doctor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'staff_role' => StaffRole::Doctor,
            'specialization' => fake()->randomElement(['Umum', 'Anak', 'Kandungan', 'Bedah', 'Penyakit Dalam']),
            'license_number' => 'STR-'.fake()->numerify('##########'),
        ]);
    }

    /**
     * Staff with midwife role.
     */
    public function midwife(): static
    {
        return $this->state(fn (array $attributes): array => [
            'staff_role' => StaffRole::Midwife,
            'specialization' => 'Kebidanan',
            'license_number' => 'STR-'.fake()->numerify('##########'),
        ]);
    }

    /**
     * Staff with nurse role.
     */
    public function nurse(): static
    {
        return $this->state(fn (array $attributes): array => [
            'staff_role' => StaffRole::Nurse,
            'specialization' => fake()->randomElement(['Umum', 'Anak', 'ICU', 'Emergency']),
            'license_number' => 'STR-'.fake()->numerify('##########'),
        ]);
    }

    /**
     * Staff with admin role.
     */
    public function staffAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'staff_role' => StaffRole::StaffAdmin,
        ]);
    }

    /**
     * Inactive staff.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
