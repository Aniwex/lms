<?php

namespace App\Resources;

use App\Models\Role;

/**
 * Класс коллекция-ресурсов модели "\App\Models\Role".
 */
class RoleCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(Role $role) {
            return RoleResource::makeArray($role);
        });
    }

}
