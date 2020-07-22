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
     *
     *
     * @param User $user
     * @param Competition $competition
     * @return bool
     */
    public function recordResults(User $user, Competition $competition)
    {
        if ($competition->round === 0) {
            return false;
        }

        if ($competition->user_id === $user->id) {
            return true;
        }

        $team_1 = Team::find(request()->team_1);
        $team_2 = Team::find(request()->team_2);

        if (($team_1->competition_id === $competition->id && $team_2->competition_id === $competition->id) &&
            ($team_1->user_id === $user->id || $team_2->user_id === $user->id) &&
            $competition->self_confirm
        ) {
            return true;
        }

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
