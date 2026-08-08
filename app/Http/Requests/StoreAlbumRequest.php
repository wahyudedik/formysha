<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_private' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
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
            'name.required' => 'Nama album wajib diisi.',
            'name.max' => 'Nama album maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'media.*.file' => 'Format file media tidak valid.',
            'media.*.max' => 'Ukuran file media maksimal 10MB.',
            'media.*.mimes' => 'Format file media tidak didukung.',
        ];
    }
}
