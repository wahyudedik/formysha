<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimelineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function prepareForValidation(): void
    {
        $tags = $this->input('tags');

        if (is_string($tags) && $tags !== '') {
            $this->merge([
                'tags' => array_map('trim', explode(',', $tags)),
            ]);
        } elseif (is_string($tags)) {
            $this->merge([
                'tags' => [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'event_date' => ['required', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'mood' => ['nullable', 'in:happy,excited,calm,sad,surprised,loved'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_featured' => ['boolean'],
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm,mp3,wav,ogg'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul kenangan wajib diisi.',
            'title.max' => 'Judul kenangan maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 5000 karakter.',
            'event_date.required' => 'Tanggal kejadian wajib diisi.',
            'event_date.date' => 'Format tanggal tidak valid.',
            'event_time.date_format' => 'Format waktu tidak valid (HH:MM).',
            'location.max' => 'Lokasi maksimal 255 karakter.',
            'mood.in' => 'Mood tidak valid.',
            'tags.array' => 'Tag harus berupa array.',
            'tags.*.string' => 'Setiap tag harus berupa teks.',
            'tags.*.max' => 'Setiap tag maksimal 50 karakter.',
            'media.*.file' => 'Format file media tidak valid.',
            'media.*.max' => 'Ukuran file media maksimal 10MB.',
            'media.*.mimes' => 'Format file media tidak didukung.',
        ];
    }
}
