<?php

namespace App\Http\Requests\Claim;

use App\Rules\PhoneNumber;
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
            'phone' => ['nullable', new PhoneNumber],
            'redirected_to' => ['string', 'nullable', 'max:255'],
            'manager_check' => ['nullable', Rule::in(['targeted', 'untargeted', 'unidentified'])],
            'client_check' => ['nullable', Rule::in(['targeted', 'untargeted', 'unidentified'])],
            'manager_comment' => ['nullable', 'string'],
            'client_comment' => ['nullable', 'string'],
            'data' => ['array', 'nullable'],
            'tags' => ['array', 'nullable'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')]
        ];
    }
}
