<?php

namespace App\Integrations\Forms\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Запрос, который приходит с формы обратной связи.
 */
class SiteFormRequest extends FormRequest
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
            'source_id'  => ['required', Rule::exists('sources', 'id')], // ID источника
            'phone'      => ['required', new PhoneNumber], // номер телефона клиента
            'data'       => ['array', 'nullable'], // дополнительные данные,
            'data.email' => ['nullable', 'email'],
        ];
    }

    /**
     * Ответ при проваленной валидации.
     */
    protected function failedValidation(Validator $validator) {

        Log::channel('forms_integration')->error("Ошибка при обработке запроса из формы обратной связи. " . print_r([
            'Ошибки валидации' => $validator->errors()->toArray(),
            'Запрос' => request()->input(),
            'URL' => request()->url(),
            'Referer' => request()->headers->get('referer')
        ], true));

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
            'source_id.exists' => 'Не существует источника (Source) с заданным ID',
        ];
    }
}
