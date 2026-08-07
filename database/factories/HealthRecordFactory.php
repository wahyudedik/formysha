<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\HealthRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthRecord>
 */
class HealthRecordFactory extends Factory
{
    protected $model = HealthRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['immunization', 'illness', 'medication', 'allergy', 'checkup', 'other'];
        $type = fake()->randomElement($types);

        $names = match ($type) {
            'immunization' => ['BCG', 'Polio 1', 'Polio 2', 'DPT 1', 'DPT 2', 'Campak', 'Hepatitis B', 'MMR'],
            'illness' => ['Demam', 'Batuk', 'Pilek', 'Diare', 'DBD', 'Cacar Air', 'ISPA'],
            'medication' => ['Parasetamol', 'Amoksisilin', 'Vitamin A', 'Zinc', 'ORS', 'Ibuprofen'],
            'allergy' => ['Alergi Susu', 'Alergi Kacang', 'Alergi Debu', 'Alergi Makanan Laut', 'Eksim'],
            'checkup' => ['Pemeriksaan Rutin', 'Pertumbuhan', 'Tumbuh Gigi', 'Pemeriksaan Mata', 'Pemeriksaan Telinga'],
            'other' => ['Kontrol', 'Vaksinasi Tambahan', 'Pemeriksaan Lab'],
        };

        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'name' => fake()->randomElement($names),
            'description' => fake()->optional(0.6)->sentence(),
            'date' => fake()->dateTimeBetween('-2 years', 'now'),
            'doctor' => fake()->optional(0.7)->name(),
            'hospital' => fake()->optional(0.7)->randomElement(['RS Husada', 'RS Mitra Keluarga', 'Klinik Sehat', 'Puskesmas', 'RS Siloam', 'RS Pondok Indah']),
            'notes' => fake()->optional(0.5)->sentence(),
            'next_date' => fake()->optional(0.4)->dateTimeBetween('now', '+6 months'),
        ];
    }

    /**
     * Set the health record type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }

    /**
     * Set type to immunization.
     */
    public function immunization(): static
    {
        return $this->ofType('immunization');
    }

    /**
     * Set type to illness.
     */
    public function illness(): static
    {
        return $this->ofType('illness');
    }

    /**
     * Set type to medication.
     */
    public function medication(): static
    {
        return $this->ofType('medication');
    }

    /**
     * Set type to allergy.
     */
    public function allergy(): static
    {
        return $this->ofType('allergy');
    }

    /**
     * Set type to checkup.
     */
    public function checkup(): static
    {
        return $this->ofType('checkup');
    }

    /**
     * Add a next_date for follow-up.
     */
    public function withNextDate(): static
    {
        return $this->state(fn () => ['next_date' => fake()->dateTimeBetween('now', '+6 months')]);
    }

    /**
     * Set a specific date.
     */
    public function atDate(string $date): static
    {
        return $this->state(fn () => ['date' => $date]);
    }
}
