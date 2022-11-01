<?php

namespace App\Http\Requests\Integration;

/**
 * Запрос на изменение данных интеграции.
 */
class UpdateIntegrationRequest extends StoreIntegrationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['string', 'nullable', 'max:255'],
            'slug' => ['string', 'nullable', 'unique:integrations,slug,'.$this->integration->id, 'max:255', 'alpha_dash'],
            'config' => ['array', 'nullable'],
            'config.*.key' => ['required', 'max:255'],
            'config.*.value' => ['required', 'max:255']
        ];
    }
}
