<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\Team;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->commissioner;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Competition $competition
     * @return mixed
     */
    public function view(User $user, Competition $competition)
    {
        return $user->id === $competition->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Competition $competition
     * @return mixed
     */
    public function update(User $user, Competition $competition)
    {
        return $user->id === $competition->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Competition $competition
     * @return mixed
     */
    public function delete(User $user, Competition $competition)
    {
        return $user->id === $competition->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Competition $competition
     * @return mixed
     */
    public function restore(User $user, Competition $competition)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Competition $competition
     * @return mixed
     */
    public function forceDelete(User $user, Competition $competition)
    {
        return false;
    }

    /**
     * Register team public request
     *
     * @param User $user
     * @param Competition $competition
     * @return bool
     */
    public function registerTeam(User $user, Competition $competition)
    {
        return !$competition->finished
            && today()->toDateString() <= $competition->registration_end;

    }

    /**
     * Register team for a competition by a commissioner.
     *
     * @param User $user
     * @param Competition $competition
     * @return bool
     */
    public function registerTeamCommissioner(User $user, Competition $competition)
    {
        return $user->id === $competition->user_id;
    }
}