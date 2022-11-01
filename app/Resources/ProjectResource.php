<?php

namespace App\Resources;

use App\Models\Project;

/**
 * Класс-ресурс модели "\App\Models\Project".
 */
class ProjectResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(Project $project) {
            $data = [
                'id' => $project->id,
                'name' => $project->name,
                'domain' => $project->domain,
                'mirrows' => $project->mirrows,
                'created_at' => $project->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $project->updated_at->format('Y-m-d H:i:s')
            ];

            if ($project->relationLoaded('users')) {
                $data['users'] = UserCollection::makeArray($project->users);
            }

            return $data;
        });
    }
}
