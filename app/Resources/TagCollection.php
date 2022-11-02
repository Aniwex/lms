<?php

namespace App\Resources;

use App\Models\Tag;

/**
 * Класс коллекция-ресурсов модели "\App\Models\Tag".
 */
class TagCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(Tag $tag) {
            return TagResource::makeArray($tag);
        });
    }

}
