<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Rules\Slug;
use Illuminate\Validation\Rule;

/**
 * Запрос на добавление нового пользователя.
 */
class StoreUserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => ['string', 'required', 'unique:users', 'max:255', new Slug],
            'password' => ['string', 'required', 'max:255', new Slug],
            'role_id' => ['integer', 'exists:roles,id'],
            'projects' => ['array', 'nullable'],
            'projects.*' => ['integer', Rule::exists('projects', 'id')]
        ];
    }
}
