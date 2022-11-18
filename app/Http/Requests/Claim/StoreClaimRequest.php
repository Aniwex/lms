<?php

namespace App\Http\Requests\Claim;

use App\Http\Requests\Request;
use App\Rules\PhoneNumber;
use App\Models\Claim;
use Illuminate\Validation\Rule;

/**
 * Запрос на добавление нового обращения (заявки).
 */
class StoreClaimRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'duration' => ['integer', 'nullable', 'min:1'],
            'datetime' => ['nullable', 'date'],
            'source_id' => ['integer', 'required', Rule::exists('sources', 'id')],
            'phone' => ['required', new PhoneNumber],
            'redirected_to' => ['string', 'nullable', 'max:255'],
            'manager_check' => ['nullable', Rule::in(array_keys(Claim::managerCheckValues()))],
            'client_check' => ['nullable', Rule::in(array_keys(Claim::clientCheckValues()))],
            'manager_comment' => ['nullable', 'string'],
            'client_comment' => ['nullable', 'string'],
            'data' => ['array', 'nullable'],
            'tags' => ['array', 'nullable'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')]
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'phone.regex' => 'Указан ненастоящий номер телефона',
            'source_id.required' => 'Укажите источник обращения',
            'source_id.exists' => 'Укажите существующий источник обращения'
        ];
    }
}
