<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;

/**
 * Запрос на изменение пользователя.
 */
class UpdateUserRequest extends StoreUserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => ['string', 'nullable', 'unique:users,login,' . $this->user->id, 'max:255'],
            'password' => ['string', 'nullable', 'max:255'],
            'role_id' => ['integer', 'exists:roles,id', 'nullable'],
            'projects' => ['array', 'nullable'],
            'projects.*' => ['integer', Rule::exists('projects', 'id')]
        ];
    }
}
