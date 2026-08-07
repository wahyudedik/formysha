<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDiaryRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'mood' => ['nullable', 'string', 'in:happy,excited,calm,sad,surprised,loved'],
            'diary_date' => ['required', 'date'],
            'weather' => ['nullable', 'string', 'in:sunny,cloudy,rainy,windy,snowy'],
            'is_private' => ['boolean'],
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
            'title.required' => 'Judul catatan wajib diisi.',
            'title.max' => 'Judul catatan maksimal 255 karakter.',
            'content.required' => 'Isi catatan wajib diisi.',
            'content.max' => 'Isi catatan maksimal 10.000 karakter.',
            'mood.in' => 'Mood tidak valid.',
            'diary_date.required' => 'Tanggal catatan wajib diisi.',
            'diary_date.date' => 'Format tanggal tidak valid.',
            'weather.in' => 'Cuaca tidak valid.',
        ];
    }
}
