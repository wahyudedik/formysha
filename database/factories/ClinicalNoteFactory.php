<?php

namespace Database\Factories;

use App\Enums\ClinicalNoteType;
use App\Models\Child;
use App\Models\ClinicalNote;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClinicalNote>
 */
class ClinicalNoteFactory extends Factory
{
    protected $model = ClinicalNote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'child_id' => Child::factory(),
            'staff_user_id' => User::factory(),
            'type' => ClinicalNoteType::Consultation,
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(3),
            'vitals' => [
                'temperature' => fake()->randomFloat(1, 36.0, 37.5).'°C',
                'weight' => fake()->randomFloat(1, 3.0, 80.0).'kg',
                'height' => fake()->randomFloat(0, 45, 170).'cm',
            ],
            'diagnosis' => fake()->sentence(5),
            'medications' => [
                ['name' => fake()->word(), 'dosage' => fake()->word(), 'frequency' => '3x sehari'],
            ],
            'attachments' => null,
        ];
    }

    /**
     * Consultation note.
     */
    public function consultation(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ClinicalNoteType::Consultation,
        ]);
    }

    /**
     * Examination note.
     */
    public function examination(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ClinicalNoteType::Examination,
        ]);
    }

    /**
     * Treatment note.
     */
    public function treatment(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ClinicalNoteType::Treatment,
        ]);
    }

    /**
     * Follow-up note.
     */
    public function followUp(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => ClinicalNoteType::FollowUp,
        ]);
    }
}
