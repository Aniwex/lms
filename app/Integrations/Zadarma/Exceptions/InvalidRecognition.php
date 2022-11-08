<?php

namespace App\Integrations\Zadarma\Exceptions;

/**
 * Ошибка при получении распознавания звонка.
 */
class InvalidRecognition extends \Exception
{
    protected $code = 422;
    protected $message = 'Ошибка при получении распознавания звонка. Массив с результатами не является валидным или не соответствует документации к API.';
}
