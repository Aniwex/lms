<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;

/**
 * Запрос на добавление нового проекта.
 */
class StoreProjectRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'nullable', 'max:255'],
            'domain' => ['string', 'required', 'unique:projects', 'max:255'],
            'mirrows' => ['array', 'nullable'],
            'mirrows.*' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'domain.unique' => 'Такой проект уже существует'
        ];
    }
}
