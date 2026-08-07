<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGrowthRequest extends FormRequest
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
            'measured_at' => ['required', 'date', 'before_or_equal:today'],
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'height_cm' => ['required', 'numeric', 'min:1', 'max:200'],
            'head_circumference_cm' => ['nullable', 'numeric', 'min:20', 'max:70'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'measured_at.required' => 'Tanggal pengukuran wajib diisi.',
            'measured_at.date' => 'Format tanggal tidak valid.',
            'measured_at.before_or_equal' => 'Tanggal pengukuran tidak boleh di masa depan.',
            'weight_kg.required' => 'Berat badan wajib diisi.',
            'weight_kg.numeric' => 'Berat badan harus berupa angka.',
            'weight_kg.min' => 'Berat badan minimal 0.1 kg.',
            'weight_kg.max' => 'Berat badan maksimal 100 kg.',
            'height_cm.required' => 'Tinggi badan wajib diisi.',
            'height_cm.numeric' => 'Tinggi badan harus berupa angka.',
            'height_cm.min' => 'Tinggi badan minimal 1 cm.',
            'height_cm.max' => 'Tinggi badan maksimal 200 cm.',
            'head_circumference_cm.numeric' => 'Lingkar kepala harus berupa angka.',
            'head_circumference_cm.min' => 'Lingkar kepala minimal 20 cm.',
            'head_circumference_cm.max' => 'Lingkar kepala maksimal 70 cm.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
