<?php

namespace App\Http\Requests\Source;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateSourceRequest extends Request
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
            'code' => ['string', 'required', 'max:255', Rule::unique('sources')->where('project_id', $this->project_id)->ignore($this->source)],
            'data' => ['array', 'nullable']
        ];
    }
}
