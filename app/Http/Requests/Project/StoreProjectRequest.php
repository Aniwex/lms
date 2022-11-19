<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'unique:projects', 'max:255'],
            'mirrows' => ['array', 'nullable'],
            'mirrows.*' => ['required', 'string', 'max:255'],
            'users' => ['array', 'nullable'],
            'users.*' => ['integer', Rule::exists('users', 'id')]
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
