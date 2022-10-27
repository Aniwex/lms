<?php

namespace App\Resources;

use App\Models\Integration;

/**
 * Класс коллекция-ресурсов модели "\App\Models\Integration".
 */
class IntegrationCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(Integration $integration) {
            return IntegrationResource::makeArray($integration);
        });
    }

}
