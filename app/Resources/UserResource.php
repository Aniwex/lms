<?php

namespace App\Resources;

use App\Models\User;

/**
 * Класс-ресурс модели "\App\Models\User".
 */
class UserResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(User $user) {
            $data = [
                'id' => $user->id,
                'login' => $user->login,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
            ];

            if ($user->relationLoaded('role')) {
                $data['role'] = RoleResource::makeArray($user->role);
            }

            if ($user->relationLoaded('projects')) {
                $data['projects'] = ProjectCollection::makeArray($user->projects);
            }

            return $data;
        });
    }
}
