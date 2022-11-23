<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Request extends FormRequest
{
    /**
     * @var bool
     */
    protected $stopOnFirstFailure = false;

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
        return parent::failedValidation($validator);
    }

    /**
     * Получить только заполненные поля запроса.
     * @return self
     */
    public function onlyFilled() : self
    {
        foreach ($this->all() as $key => $value) {
            if (!$value || is_null($value) || empty($value)) {
                $this->getInputSource()->remove($key);
                $this->request->remove($key);
                $this->query->remove($key);
            }
        }
        return $this;
    }
}
