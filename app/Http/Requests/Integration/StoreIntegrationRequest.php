<?php

namespace App\Http\Requests\Integration;

use App\Http\Requests\Request;
use App\Rules\Slug;

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
            'slug' => ['required', 'string', 'unique:integrations', 'max:255', new Slug],
            'config' => ['array', 'nullable'],
            'config.*.key' => ['required', 'max:255'],
            'config.*.value' => ['required', 'max:255']
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
