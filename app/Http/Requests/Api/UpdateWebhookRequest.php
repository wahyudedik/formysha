<?php

namespace App\Http\Requests\Api;

use App\Services\WebhookService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebhookRequest extends FormRequest
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
     * @return array<string, array<int, Rule|string>>
     */
    public function rules(): array
    {
        return [
            'url' => ['sometimes', 'required', 'url', 'max:2048'],
            'events' => ['sometimes', 'required', 'array', 'min:1'],
            'events.*' => ['string', 'in:'.implode(',', WebhookService::AVAILABLE_EVENTS)],
            'secret' => ['sometimes', 'required', 'string', 'min:32', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
