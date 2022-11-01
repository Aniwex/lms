<?php

namespace App\Http\Requests\User;

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
        $rules = parent::rules();
        $rules['login'] = ['string', 'required', 'unique:users,login,' . $this->user->id, 'max:255'];
        return $rules;
    }
}
