<?php

namespace App\Policies;

use App\Models\Source;
use App\Models\User;
use App\Policies\Traits\HandlesProjectAuthorization;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Управление доступом к источникам.
 */
class SourcePolicy
{
    use HandlesAuthorization, HandlesProjectAuthorization;

    /**
     * Относится ли источник к проекту.
     * @param Source $source
     * @return bool
     */
    protected function sourceBelongsToProject(Source $source) : bool
    {
        $project = current_project();
        if (!$project) return true;

        return $source->project_id == $project->id;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $this->userHasAccessToProject($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Source $source)
    {
        return $this->userHasAccessToProject($user) && $this->sourceBelongsToProject($source);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->userHasAccessToProject($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Source $source)
    {
        return $this->userHasAccessToProject($user) && $this->sourceBelongsToProject($source);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Source $source)
    {
        return $this->userHasAccessToProject($user) && $this->sourceBelongsToProject($source);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Source $source)
    {
        return $this->userHasAccessToProject($user) && $this->sourceBelongsToProject($source);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Source $source)
    {
        return $this->userHasAccessToProject($user) && $this->sourceBelongsToProject($source);
    }
}
