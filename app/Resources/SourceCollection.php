<?php

namespace App\Resources;

use App\Models\Source;

/**
 * Класс коллекция-ресурсов модели "\App\Models\Source".
 */
class SourceCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(Source $source) {
            return SourceResource::makeArray($source);
        });
    }

}
