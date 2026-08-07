<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:2000'],
            'event_date' => ['required', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'event_type' => ['nullable', 'in:birthday,immunization,checkup,school,vaccination,holiday,other'],
            'is_recurring' => ['boolean'],
            'recurrence_pattern' => ['nullable', 'string', 'max:100'],
            'reminder_at' => ['nullable', 'date'],
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
            'title.required' => 'Judul acara wajib diisi.',
            'title.max' => 'Judul acara maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'event_date.required' => 'Tanggal acara wajib diisi.',
            'event_date.date' => 'Format tanggal tidak valid.',
            'event_time.date_format' => 'Format waktu tidak valid (HH:MM).',
            'event_type.in' => 'Jenis acara tidak valid.',
            'recurrence_pattern.max' => 'Pola pengulangan maksimal 100 karakter.',
        ];
    }
}
