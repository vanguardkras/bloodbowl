<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Team;
use Illuminate\Http\Request;

class AddResultsAjaxController extends Controller
{
    /**
     * Check if the user is the commissioner
     *
     * @param Competition $competition
     * @return bool
     */
    public function isCommissioner(Competition $competition)
    {
        return auth()->user()->id === $competition->user_id;
    }

    /**
     * Get the users team participating in the competition
     *
     * @param Competition $competition
     * @return mixed
     */
    public function getTeam(Competition $competition)
    {
        return auth()->user()->teams()
            ->where('competition_id', $competition->id)->first('id')->id;
    }

    /**
     * Get possible opponents for the team
     *
     * @param Competition $competition
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \ReflectionException
     */
    public function getPossibleOpponents(Competition $competition, Team $team)
    {
        return response()->json($competition->getPossibleOpponents($team));
    }
}
