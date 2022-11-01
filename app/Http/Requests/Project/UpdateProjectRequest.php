<?php

namespace App\Http\Requests\Project;

use Illuminate\Validation\Rule;

/**
 * Запрос на изменение данных по проекту.
 */
class UpdateProjectRequest extends StoreProjectRequest
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
            'mirrows' => ['array', 'nullable'],
            'mirrows.*' => ['required', 'string', 'max:255'],
            'domain' => ['string', 'nullable', 'unique:projects,domain,'.$this->project->id, 'max:255'],
            'users' => ['array', 'nullable'],
            'users.*' => ['integer', Rule::exists('users', 'id')]
        ];
    }
}
