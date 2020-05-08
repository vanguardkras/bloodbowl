<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitionTeamRegisterRequest;
use App\Models\Competition;
use App\Models\Team;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Competition page
     *
     * @param Competition $competition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function competition(Competition $competition)
    {
        $competition->load(['user' => function ($query) {
            $query->select('id', 'username');
        }])->loadCount('teams')->load('races');

        if (auth()->check()) {
            auth()->user()->registered_team = auth()->user()->registeredTeam($competition->id);
        }
        return view('competition', compact('competition'));
    }

    /**
     * Main page
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function main(Request $request)
    {
        $open_registrations = Competition::getOpenRegistration();
        return view('main', compact('open_registrations'));
    }

    /**
     * Apply for a competition.
     *
     * @param CompetitionTeamRegisterRequest $request
     * @param Competition $competition
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function registerTeam(CompetitionTeamRegisterRequest $request, Competition $competition)
    {
        $this->authorize('registerTeam', $competition);
        $competition->registeredTeams()->sync([$request->team_id]);
        return back();
    }
}
