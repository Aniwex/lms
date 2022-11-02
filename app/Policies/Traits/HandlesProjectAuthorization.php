<?php

namespace App\Policies\Traits;

use App\Models\User;

trait HandlesProjectAuthorization
{
    /**
     * Имеет ли пользователь доступ к проекту.
     * @param User $user
     * @return bool
     */
    protected function userHasAccessToProject(User $user) : bool
    {
        $project = current_project();
        if (!$project) return true;

        return $user->isAdmin() || $user->isProjectManager($project);
    }
}