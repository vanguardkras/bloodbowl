<?php

namespace App\Policies;

use App\Models\MatchLog;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatchLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param MatchLog $matchLog
     * @return mixed
     */
    public function confirmRejectResults(User $user, MatchLog $matchLog)
    {
        $competition = $matchLog->competition()->first(['id', 'user_id', 'self_confirm']);

        if ($user->id === $competition->user_id) {
            return true;
        }

        if ($user->id === $matchLog->user_id || !$competition->self_confirm) {
            return false;
        }

        $team_1 = $matchLog->teamLeft()->first('user_id');
        $team_2 = $matchLog->teamRight()->first('user_id');

        dd('test');
        return $user->id === $team_1->user_id || $user->id === $team_2->user_id;
    }
}
