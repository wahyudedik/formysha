<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileTypes = ['photo', 'video', 'audio', 'document'];
        $fileType = fake()->randomElement($fileTypes);

        $extensions = match ($fileType) {
            'photo' => ['jpg', 'jpeg', 'png', 'webp'],
            'video' => ['mp4', 'mov', 'avi'],
            'audio' => ['mp3', 'wav', 'ogg'],
            'document' => ['pdf', 'doc', 'docx'],
        };

        $ext = fake()->randomElement($extensions);
        $fileName = fake()->uuid().'.'.$ext;

        return [
            'mediable_type' => Child::class,
            'mediable_id' => Child::factory(),
            'file_path' => 'media/'.$fileName,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => fake()->numberBetween(1024, 10_485_760),
            'alt_text' => $fileType === 'photo' ? fake()->sentence(3) : null,
            'sort_order' => 0,
        ];
    }

    /**
     * Set file type to photo.
     */
    public function photo(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'photo',
            'file_name' => fake()->uuid().'.jpg',
            'file_path' => 'media/'.fake()->uuid().'.jpg',
            'alt_text' => fake()->sentence(3),
        ]);
    }

    /**
     * Set file type to video.
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'video',
            'file_name' => fake()->uuid().'.mp4',
            'file_path' => 'media/'.fake()->uuid().'.mp4',
        ]);
    }

    /**
     * Set file type to audio.
     */
    public function audio(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'audio',
            'file_name' => fake()->uuid().'.mp3',
            'file_path' => 'media/'.fake()->uuid().'.mp3',
        ]);
    }

    /**
     * Set file type to document.
     */
    public function document(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'document',
            'file_name' => fake()->uuid().'.pdf',
            'file_path' => 'media/'.fake()->uuid().'.pdf',
        ]);
    }
}
