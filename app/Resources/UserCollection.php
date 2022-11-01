<?php

namespace App\Resources;

use App\Models\User;

/**
 * Класс коллекция-ресурсов модели "\App\Models\User".
 */
class UserCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(User $user) {
            return UserResource::makeArray($user);
        });
    }

}
