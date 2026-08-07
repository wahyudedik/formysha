<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthRecordRequest extends FormRequest
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
            'type' => ['required', 'in:immunization,illness,medication,allergy,checkup,other'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'doctor' => ['nullable', 'string', 'max:255'],
            'hospital' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_date' => ['nullable', 'date', 'after_or_equal:today'],
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
            'type.required' => 'Jenis catatan kesehatan wajib diisi.',
            'type.in' => 'Jenis catatan kesehatan tidak valid.',
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'doctor.max' => 'Nama dokter maksimal 255 karakter.',
            'hospital.max' => 'Nama rumah sakit/klinik maksimal 255 karakter.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'next_date.date' => 'Format tanggal berikutnya tidak valid.',
            'next_date.after_or_equal' => 'Tanggal berikutnya harus hari ini atau di masa depan.',
        ];
    }
}
