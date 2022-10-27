<?php

namespace App\Http\Requests;

use App\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Request extends FormRequest
{
    /**
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Обработка ошибок валидации.
     * @param Validator $validator
     * @throws HttpResponseException
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $error = Response::error(422)->message(
            $validator->errors()->first()
        );

        throw new HttpResponseException($error->json());
    }
}
