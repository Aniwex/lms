<?php

namespace App\Resources;

use League\Fractal\Manager;

/**
 * Абстрактный класс коллекции ресурсов.
 */
abstract class AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    abstract public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection;

    /**
     * Создание коллекции ресурсов из коллекции моделей и преобразование в массив.
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    public static function makeArray(\Illuminate\Support\Collection $collection) : array
    {
        $resource = static::make($collection);
        return (new Manager)->createData($resource)->toArray()['data'];
    }
}
