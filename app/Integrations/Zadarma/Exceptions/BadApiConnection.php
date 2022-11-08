<?php

namespace App\Integrations\Zadarma\Exceptions;

/**
 * Ошибка при подключении к API задармы.
 */
class BadApiConnection extends \Exception
{
    protected $code = 400;
    protected $message = 'Ошибка при подключении к API задармы. Возможно не хватает конфигурационных параметров в источнике. Или источник не существует (не связан) с обращением';
}
