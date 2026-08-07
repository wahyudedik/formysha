<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Child>
 */
class ChildFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->firstName();
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'nickname' => fake()->optional(0.6)->firstName(),
            'gender' => $gender,
            'date_of_birth' => fake()->dateTimeBetween('-5 years', 'now'),
            'place_of_birth' => fake()->city(),
            'blood_type' => fake()->optional(0.7)->randomElement(['A', 'B', 'AB', 'O']),
            'photo' => null,
            'bio' => fake()->optional(0.5)->sentence(6),
            'is_public' => fake()->boolean(20),
            'public_profile_data' => null,
        ];
    }

    /**
     * Indicate that the child is male.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes): array => [
            'gender' => 'male',
        ]);
    }

    /**
     * Indicate that the child is female.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes): array => [
            'gender' => 'female',
        ]);
    }

    /**
     * Indicate that the child has a public profile.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_public' => true,
        ]);
    }
}
