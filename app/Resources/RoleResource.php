<?php

namespace App\Resources;

use App\Models\Role;

/**
 * Класс-ресурс модели "\App\Models\Role".
 */
class RoleResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(Role $role) {
            return [
                'id' => $role->id,
                'slug' => $role->slug,
                'title' => $role->title
            ];
        });
    }
}
