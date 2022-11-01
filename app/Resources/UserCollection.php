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
            $data = [
                'id' => $user->id,
                'login' => $user->login,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
            ];

            if ($user->relationLoaded('role')) {
                $data['role'] = RoleResource::makeArray($user->role);
            }

            return $data;
        });
    }

}
