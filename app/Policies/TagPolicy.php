<?php

namespace App\Policies;

use App\Models\Source;
use App\Models\Tag;
use App\Models\User;
use App\Policies\Traits\HandlesProjectAuthorization;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Управление доступом к тегам обращений.
 */
class TagPolicy
{
    use HandlesAuthorization, HandlesProjectAuthorization;

    /**
     * Относится ли тег к проекту.
     * @param Tag $tag
     * @return bool
     */
    protected function tagBelongsToProject(Tag $tag) : bool
    {
        $project = current_project();
        if (!$project) return true;

        return $tag->project_id == $project->id;
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
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Tag $tag)
    {
        return $this->userHasAccessToProject($user) && $this->tagBelongsToProject($tag);
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
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Tag $tag)
    {
        return $this->userHasAccessToProject($user) && $this->tagBelongsToProject($tag);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Tag $tag)
    {
        return $this->userHasAccessToProject($user) && $this->tagBelongsToProject($tag);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Tag $tag)
    {
        return $this->userHasAccessToProject($user) && $this->tagBelongsToProject($tag);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Tag $tag)
    {
        return $this->userHasAccessToProject($user) && $this->tagBelongsToProject($tag);
    }
}
