<?php

namespace App\Http\Requests\Tag;

/**
 * Запрос на изменение тега обращения.
 */
class UpdateTagRequest extends StoreTagRequest
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
            'objective' => ['nullable', 'boolean'],
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
