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
        $rules = parent::rules();
        $rules['slug'] = ['string', 'required', 'unique:integrations,slug,'.$this->integration->id, 'max:255'];
        return $rules;
    }
}
