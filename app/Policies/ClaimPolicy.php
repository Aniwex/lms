<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;
use App\Policies\Traits\HandlesProjectAuthorization;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Управление доступом к заявкам (обращениям).
 */
class ClaimPolicy
{
    use HandlesAuthorization, HandlesProjectAuthorization;

    /**
     * Относится ли обращение к проекту.
     * @param Claim $claim
     * @return bool
     */
    protected function claimBelongsToProject(Claim $claim) : bool
    {
        $project = current_project();
        if (!$project) return true;

        return $claim->project_id == $project->id;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $this->userHasAccessToProject($user) || $user->isClient();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Claim $claim)
    {
        return ($this->userHasAccessToProject($user) && $this->claimBelongsToProject($claim)) || $user->isClient();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Claim $claim)
    {
        return ($this->userHasAccessToProject($user) && $this->claimBelongsToProject($claim)) || $user->isClient();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Claim $claim)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Claim $claim)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Claim $claim)
    {
        return $user->isAdmin();
    }
}
