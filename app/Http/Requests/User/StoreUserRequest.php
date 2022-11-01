<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

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
            'login' => ['string', 'required', 'unique:users', 'max:255'],
            'password' => ['string', 'required', 'max:255'],
            'role_id' => ['integer', 'exists:roles,id']
        ];
    }
}
