<?php

namespace App\Integrations\Marquiz\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Уведомление о прохождении нового квиза. Отправляется через хук интеграции с МАРКВИЗОМ.
 */
class NewQuizRequest extends FormRequest
{
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
            'contacts.name'  => 'nullable',
            'contacts.email' => 'nullable|email',
            'contacts.phone' => 'required|numeric',

            // идентификационные данные для определения источника
            'quiz.id'        => ['required', Rule::exists('sources', 'data->quiz_id')],
            'quiz.name'      => 'nullable',

            // дата создания
            'created'        => 'required|date',

            // диалог (ответы на вопросы)
            'answers'        => 'required|array',
            'answers.*.q'    => 'required',
            'answers.*.a'    => 'required',

            // дополнительные данные
            'extra.href'     => 'nullable|url', // урл страницы, где был пройден квиз
            'extra.utm'      => 'nullable', // utm метки
            'extra.referer'  => 'nullable|url', // откуда был переход
            'extra.ip'       => 'nullable|ip', // ip адрес
            'result'         => 'nullable' // дополнительные данные квиза
        ];
    }

    /**
     * Ответ при проваленной валидации.
     */
    protected function failedValidation(Validator $validator) {

        Log::channel('marquiz')->error("Ошибка при обработке запроса из марквиза.", [
            'Ошибки валидации' => $validator->errors()->toArray(),
            'Запрос марквиза' => request()->input()
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
            'quiz.id.exists' => 'Не существует источника (Source) с заданным значением',
        ];
    }
}
