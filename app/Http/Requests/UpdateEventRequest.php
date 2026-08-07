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
     * @return array<string, ValidationRule|array<mixed>, string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'event_date' => ['required', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'event_type' => ['required', 'string', 'in:birthday,immunization,appointment,school,other'],
            'is_recurring' => ['boolean'],
            'recurrence_pattern' => ['nullable', 'string', 'in:weekly,monthly,yearly'],
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
            'event_date.required' => 'Tanggal acara wajib diisi.',
            'event_date.date' => 'Format tanggal tidak valid.',
            'event_type.required' => 'Jenis acara wajib dipilih.',
            'event_type.in' => 'Jenis acara tidak valid.',
            'event_time.date_format' => 'Format waktu tidak valid (HH:mm).',
            'recurrence_pattern.in' => 'Pola pengulangan tidak valid.',
        ];
    }
}
