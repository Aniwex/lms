<?php

namespace App\Http\Requests\Claim;

use App\Models\Claim;
use Illuminate\Validation\Rule;

/**
 * Запрос на изменение данных по заявке.
 */
class UpdateClaimRequest extends StoreClaimRequest
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
            'source_id' => ['integer', 'nullable', Rule::exists('sources', 'id')],
            'phone' => ['nullable', 'regex:/^((8|\+374|\+994|\+995|\+375|\+7|\+380|\+38|\+996|\+998|\+993)[\- ]?)?\(?\d{3,5}\)?[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}(([\- ]?\d{1})?[\- ]?\d{1})?$/'],
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
}
