<?php

namespace App\Resources;

use League\Fractal\Manager;

/**
 * Абстрактный класс ресурса.
 */
abstract class AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    abstract public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item;

    /**
     * Создание ресурса из модели и преобразование в массив.
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    public static function makeArray(\Illuminate\Database\Eloquent\Model $model) : array
    {
        $resource = static::make($model);
        return (new Manager)->createData($resource)->toArray()['data'];
    }
}
