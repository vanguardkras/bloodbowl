<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitionTeamRegisterRequest;
use App\Http\Requests\MatchResultsRequest;
use App\Models\Competition;
use App\User;
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
            auth()->user()->approved_team = auth()->user()->approvedTeam($competition->id);
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

    /**
     * Add new match results to the competition
     *
     * @param MatchResultsRequest $request
     * @param Competition $competition
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \ReflectionException
     */
    public function recordResults(MatchResultsRequest $request, Competition $competition)
    {
        $this->authorize('recordResults', $competition);
        $competition->recordResults($request->all());
        return back();
    }

    /**
     * A coach page.
     *
     * @param User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function user(User $user)
    {
        $user->load('teams')->with('races');
        return view('user', compact('user'));
    }
}
