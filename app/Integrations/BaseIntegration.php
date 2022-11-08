<?php

namespace App\Integrations;

use App\Models\Source;

/**
 * Абстрактный класс "Интеграция". От него наследуется все классы для интеграции с различными сервисами. Реализует логику работы с данными для конкретного источника в зависимости от интеграции.
 */
abstract class BaseIntegration
{
    /**
     * @var Source Источник обращения
     */
    protected Source $source;

    /**
     * @param Source $source
     */
    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    /**
     * @return array Поля для источника, связанные с интеграцией
     */
    abstract public function fields() : array;
}
