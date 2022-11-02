<?php

namespace App\Integrations\Zadarma\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class NotifyRequest extends FormRequest
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
        $rules = [
            // событие (обрабатываем только окончание звонка и уведомление о том что распознавание текста готово)
            'event' => ['required', Rule::in(['NOTIFY_END', 'NOTIFY_RECORD'])],
            // идентификатор для получения распознавания текста
            'call_id_with_rec' => [
                Rule::requiredIf(function () {
                    return $this->event == 'NOTIFY_RECORD';
                }),
            ],

            // источник
            'called_did' => [
                Rule::requiredIf(function () {
                    return $this->event == 'NOTIFY_END';
                }),
                'numeric',
                Rule::exists('sources', 'data->phone')
            ],
            // пользователь
            'caller_id' => [
                Rule::requiredIf(function () {
                    return $this->event == 'NOTIFY_END';
                }),
            ],
            // продолжительность звонка
            'duration' => [
                Rule::requiredIf(function () {
                    return $this->event == 'NOTIFY_END';
                }),
                'numeric'
            ],
            // дата и время
            'call_start' => [
                Rule::requiredIf(function () {
                    return $this->event == 'NOTIFY_END';
                }),
                'date'
            ],
        ];

        // при получении распознавания текста, у нас в системе уже должна существовать заявка связанная по идентификатору
        if ($this->event == 'NOTIFY_RECORD') {
            /** @todo почему-то в laravel нет метода Rule::existsIf */
            $rules['call_id_with_rec'][] = Rule::exists('claims', 'data->zadarma_call_id');
        }

        return $rules;
    }

    /**
     * Ответ при проваленной валидации.
     */
    protected function failedValidation(Validator $validator) {

        Log::channel('zadarma')->error("Ошибка при обработке запроса из задармы.", [
            'Ошибки валидации' => $validator->errors()->toArray(),
            'Запрос задармы' => request()->input()
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
            'event.in'                => 'Допустимые значения для поля event: NOTIFY_END, NOTIFY_RECORD',
            'call_id_with_rec.exists' => 'Не найдено обращение (Claim) по заданному значению',
            'called_did.exists'       => 'Не найден источник (Source) по заданному значению'
        ];
    }
}
