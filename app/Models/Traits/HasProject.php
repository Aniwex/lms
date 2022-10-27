<?php

namespace App\Models\Traits;

use App\Models\Project;

/**
 * Трейт для объектов, которые имеют связь с проектом.
 */
trait HasProject
{
    public static function bootHasProject()
    {
        // при создании нового объекта, связываем с текущим выбранным проектом.
        static::creating(function ($object) {
            if (!$object->project_id) {
                $object->project_id = project();
            }
        });
    }

    /**
     * Связь с проектами.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() {
        return parent::belongsTo(Project::class);
    }

    /**
     * Фильтр по проекту.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param int $projectId ID проекта
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProject($query, int $projectId = null) {

        if (!$projectId)
            $projectId = project();

        return $query->whereProjectId($projectId);
    }
}
