<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\Request;

/**
 * Запрос на добавление нового тега обращения.
 */
class StoreTagRequest extends Request
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
            'objective' => ['required', 'boolean'],
            'client_plus_words' => ['array', 'nullable'],
            'client_plus_words.*' => ['required', 'string', 'max:255'],
            'client_minus_words' => ['array', 'nullable'],
            'client_minus_words.*' => ['required', 'string', 'max:255'],
            'operator_plus_words' => ['array', 'nullable'],
            'operator_plus_words.*' => ['required', 'string', 'max:255'],
            'operator_minus_words' => ['array', 'nullable'],
            'operator_minus_words.*' => ['required', 'string', 'max:255'],
        ];
    }
}
