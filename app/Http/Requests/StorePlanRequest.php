<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePlanRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:plans,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_yearly' => ['nullable', 'integer', 'min:0'],
            'max_children' => ['required', 'integer', 'min:-1'],
            'max_photos' => ['required', 'integer', 'min:-1'],
            'max_videos' => ['required', 'integer', 'min:-1'],
            'max_storage_mb' => ['required', 'integer', 'min:-1'],
            'max_family_members' => ['nullable', 'integer', 'min:-1'],
            'max_export_per_day' => ['required', 'integer', 'min:-1'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('slug') || $this->input('slug') === '') {
            $this->merge([
                'slug' => Str::slug($this->input('name', '')),
            ]);
        }
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama paket wajib diisi.',
            'name.max' => 'Nama paket maksimal 255 karakter.',
            'slug.unique' => 'Slug sudah digunakan oleh paket lain.',
            'price_monthly.required' => 'Harga bulanan wajib diisi.',
            'price_monthly.integer' => 'Harga bulanan harus berupa angka.',
            'price_monthly.min' => 'Harga bulanan tidak boleh negatif.',
            'max_children.required' => 'Batas jumlah anak wajib diisi.',
            'max_photos.required' => 'Batas jumlah foto wajib diisi.',
            'max_videos.required' => 'Batas jumlah video wajib diisi.',
            'max_storage_mb.required' => 'Batas penyimpanan wajib diisi.',
            'max_export_per_day.required' => 'Batas ekspor per hari wajib diisi.',
        ];
    }
}
