<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamilyMember>
 */
class FamilyMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relationship = fake()->randomElement(['father', 'mother', 'guardian', 'grandfather', 'grandmother', 'sibling', 'other']);

        return [
            'child_id' => Child::factory(),
            'user_id' => null,
            'name' => fake()->name(),
            'relationship' => $relationship,
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'email' => fake()->optional(0.5)->safeEmail(),
            'photo' => null,
            'is_primary' => fake()->boolean(30),
        ];
    }

    /**
     * Indicate that the family member is the father.
     */
    public function father(): static
    {
        return $this->state(fn (array $attributes): array => [
            'relationship' => 'father',
        ]);
    }

    /**
     * Indicate that the family member is the mother.
     */
    public function mother(): static
    {
        return $this->state(fn (array $attributes): array => [
            'relationship' => 'mother',
        ]);
    }

    /**
     * Indicate that the family member is linked to a user account.
     */
    public function linked(): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the family member is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_primary' => true,
        ]);
    }
}
