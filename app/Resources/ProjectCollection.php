<?php

namespace App\Resources;

use App\Models\Project;

/**
 * Класс коллекция-ресурсов модели "\App\Models\Project".
 */
class ProjectCollection extends AbstractCollection
{
    /**
     * @param \Illuminate\Support\Collection $items
     * @return \League\Fractal\Resource\Collection
     */
    public static function make(\Illuminate\Support\Collection $items) : \League\Fractal\Resource\Collection
    {
        return new \League\Fractal\Resource\Collection($items, function(Project $project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'domain' => $project->domain,
                'mirrows' => $project->mirrows,
                'created_at' => $project->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $project->updated_at->format('Y-m-d H:i:s')
            ];
        });
    }

}
