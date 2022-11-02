<?php

namespace App\Http\Requests\Source;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Запрос на добавление нового источника обращения.
 */
class StoreSourceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'required', 'max:255'],
            'integration_id' => ['integer', 'exists:integrations,id'],
            'code' => ['string', 'required', 'max:255', Rule::unique('sources')->where('project_id', $this->project_id)],
            'data' => ['array', 'nullable']
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'code.unique' => 'Такой источник уже существует в проекте'
        ];
    }
}
