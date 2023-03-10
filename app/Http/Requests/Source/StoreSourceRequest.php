<?php

namespace App\Http\Requests\Source;

use App\Http\Requests\Request;
use App\Rules\Slug;
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
            'name' => ['required', 'string', 'max:255'],
            'integration_id' => ['integer', 'exists:integrations,id'],
            'code' => ['required', 'string', 'max:255', Rule::unique('sources')->where('project_id', $this->project->id), new Slug],
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
