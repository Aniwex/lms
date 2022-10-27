<?php

namespace App\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;

/**
 * Класс API Response.
 */
class Response implements Responsable
{
    /**
     * @var string Возвращаемое сообщение
     */
    public string $message = '';
    
    /**
     * @var array Возвращаемые данные
     */
    public array $data = [];

    /**
     * @var bool Статус ответа
     */
    public bool $status = true;

    /**
     * @var string[] Массив ошибок если такие есть
     */
    public array $errors = [];

    /**
     * @var int Код-статус ответа
     */
    public int $code = 200;

    /** deny construct */
    private function __construct() { }

    /** deny cloone */
    private function __clone() { }

    /**
     * Создать экземпляр объекта api response.
     * @return self
     */
    public static function make() : self
    {
        return new self();
    }

    /**
     * @return self Успешный ответ
     */
    public static function success() : self
    {
        $self = self::make();
        $self->status = true;
        $self->code = 200;
        return $self;
    }

    /**
     * @param int $code
     * @return self Ответ с ошибкой
     */
    public static function error(int $code = 500) : self
    {
        $self = self::make();
        $self->status = false;
        $self->code = $code;
        return $self;
    }

    /**
     * Добавить сообщение в ответ.
     * @param string $message
     * @return self Ответ с сообщением
     */
    public function message(string $message) : self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Добавить данные в ответ.
     * @param mixed $data
     * @return self Ответ с возвращаемыми данными
     */
    public function data($data) : self
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        $this->data = $data;
        return $this;
    }

    /**
     * Добавить ошибки в ответ.
     * @param array $errors
     * @return self Ответ с ошибками
     */
    public function errors(array $errors) : self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Установить статус-код ответа.
     * @param int $code
     * @return self Ответ с указанным статус-кодом.
     */
    public function code(int $code) : self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return array Ответ в виде массива
     */
    public function array() : array
    {
        $response = [];

        if ($this->status && empty($this->data)) {
            $response['success'] = true;
        }

        if ($this->message) {
            $response[$this->status ? 'message' : 'error'] = $this->message;
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        if (!empty($this->data)) {
            foreach ($this->data as $key => $val) {
                $response[$key] = $val;
            }
        }

        return $response;
    }

    /**
     * Преобразует текущий объект в HTTP ответ из приложения в формате json.
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function json()
    {
        return response()->json(
            $this->array(), 
            $this->code
        );
    }

    /**
     * Dump and die API response.
     * @return never
     */
    public function dd()
    {
        dd($this);
    }

    /**
     * @return string
     */
    public function toResponse($request)
    {
        return $this->json();
    }
}
