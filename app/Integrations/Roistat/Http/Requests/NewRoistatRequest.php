<?php

namespace App\Integrations\Roistat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Уведомление о прохождении нового квиза. Отправляется через хук интеграции с ROISTAT.
 */
class NewRoistatRequest extends FormRequest{
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // контактные данные
            'name'  => 'nullable',
            'phone' => 'required|numeric',

            // идентификационные данные для определения источника
            'id'        => 'required',

            // дата визита
            'date'        => 'required|date',

            //данные захвата
            'visit_id' => 'numeric', // номер визита
            'page' => 'nullable', // страница захвата
            'marker'=>'nullable', // источник визита
            'city'=>'nullable', // город визита
            'country'=>'nullable', // страна визита
            'ip'=>'nullable|ip', // ip посетителя 
            'domain'=>'nullable', // домен сайта
            'landing_page' =>'nullable', //посадочная страница
            'referrer' => 'nullable', //источник перехода

        ];
    }

    /**
     * Ответ при проваленной валидации.
     */
    protected function failedValidation(Validator $validator) {

        Log::channel('roistat')->error("Ошибка при обработке запроса из Roistat.", [
            'Ошибки валидации' => $validator->errors()->toArray(),
            'Запрос Roistat' => request()->input()
        ]);

        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    /**
     * Валидационные сообщения.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.exists' => 'Не существует источника (Source) с заданным значением',
        ];
    }
}
