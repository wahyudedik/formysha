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
            'type' => ['required', 'in:immunization,disease,medication,checkup,allergy,surgery,other'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'doctor' => ['nullable', 'string', 'max:255'],
            'hospital' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'next_date' => ['nullable', 'date', 'after_or_equal:date'],
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
            'type.required' => 'Jenis catatan kesehatan wajib dipilih.',
            'type.in' => 'Jenis catatan kesehatan tidak valid.',
            'name.required' => 'Nama catatan wajib diisi.',
            'name.max' => 'Nama catatan maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'doctor.max' => 'Nama dokter maksimal 255 karakter.',
            'hospital.max' => 'Nama rumah sakit maksimal 255 karakter.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'next_date.after_or_equal' => 'Tanggal berikutnya tidak boleh sebelum tanggal periksa.',
        ];
    }
}
