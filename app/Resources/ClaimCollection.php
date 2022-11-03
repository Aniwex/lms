<?php

namespace App\Resources;

use App\Models\Claim;

/**
 * Класс коллекция-ресурсов модели "\App\Models\Claim".
 */
class ClaimCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(Claim $claim) {
            return ClaimResource::makeArray($claim);
        });
    }

}
