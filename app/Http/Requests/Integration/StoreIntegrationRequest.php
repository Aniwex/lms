<?php

namespace App\Http\Requests\Integration;

use App\Http\Requests\Request;

/**
 * Запрос на добавление новой интеграции.
 */
class StoreIntegrationRequest extends Request
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
            'slug' => ['string', 'required', 'unique:integrations', 'max:255'],
            'config' => ['array', 'nullable']
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'slug.unique' => 'Такая интеграция уже существует'
        ];
    }
}
