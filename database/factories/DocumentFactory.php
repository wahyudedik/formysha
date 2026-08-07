<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([
            'birth_certificate', 'family_card', 'kia', 'bpjs',
            'passport', 'certificate', 'report_card', 'other',
        ]);

        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'name' => fake()->words(3, true).'.'.fake()->fileExtension,
            'type' => $type,
            'description' => fake()->optional(0.5)->sentence(),
            'file_path' => 'documents/'.fake()->uuid().'.pdf',
            'file_name' => fake()->words(3, true).'.pdf',
            'file_size' => fake()->numberBetween(1024, 10485760),
            'issued_date' => fake()->boolean(70) ? fake()->dateTimeThisYear()->format('Y-m-d') : null,
            'expiry_date' => fake()->boolean(40) ? fake()->dateTimeThisYear('+2 year')->format('Y-m-d') : null,
            'is_private' => true,
        ];
    }

    /**
     * Mark the document as public.
     */
    public function public(): static
    {
        return $this->state(fn () => ['is_private' => false]);
    }

    /**
     * Mark the document as private.
     */
    public function private(): static
    {
        return $this->state(fn () => ['is_private' => true]);
    }

    /**
     * Set a specific document type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }
}
