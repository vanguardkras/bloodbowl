<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitionTeamRegisterRequest;
use App\Http\Requests\MatchResultsRequest;
use App\Models\Competition;
use App\Models\MatchLog;
use App\User;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Competition page
     *
     * @param Competition $competition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \ReflectionException
     */
    public function competition(Competition $competition)
    {
        $competition
            ->load(['user' => function ($query) {
                $query->select('id', 'username');
            }])
            ->loadCount('teams')
            ->load('races', 'trophies.team.race');

        $competition->setStrategy();

        if (auth()->check() && (!$competition->round || ($competition->type === 'open_league' && !$competition->registration_end))) {
            auth()->user()->registered_team = auth()->user()->registeredTeam($competition->id);
            auth()->user()->approved_team = auth()->user()->approvedTeam($competition->id);
        }

        if (auth()->check() && $competition->round) {
            $isParticipant = auth()->user()->teams()
                ->where('competition_id', $competition->id)->get()->isNotEmpty();
        } else {
            $isParticipant = false;
        }

        $unconfirmed = collect([]);

        if (auth()->check()) {
            if ($competition->user_id === auth()->user()->id) {
                $unconfirmed = $competition->matchLogs()->where('confirmed', false)
                    ->with(['teamLeft.user', 'teamRight.user'])->get();
                $isParticipant = true;
            } elseif ($isParticipant && $competition->self_confirm) {
                $user_team = auth()->user()->teams()->where('competition_id', $competition->id)->first('id');
                $unconfirmed = $competition->matchLogs()
                    ->where([['confirmed', false], ['user_id', '!=', auth()->user()->id]])
                    ->where(function ($query) use ($user_team) {
                        $query->where('team_id_1', $user_team->id)
                            ->orWhere('team_id_2', $user_team->id);
                    })
                    ->with(['teamLeft.user', 'teamRight.user'])->get();
            }
        }


        $histories = $competition
            ->histories()
            ->with('team_1.user', 'team_2.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('competition', compact([
            'competition',
            'histories',
            'isParticipant',
            'unconfirmed'
        ]));
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

        $ongoing_competitions = collect([]);

        if (auth()->check()) {
            $ongoing_competitions = auth()->user()
                ->teams()
                ->with('competition')
                ->get();
        }

        $recent_competitions = Competition::select(['id', 'name', 'logo', 'type'])
            ->where('finished', '!=', null)
            ->orderBy('finished', 'desc')
            ->limit(5)->get();

        return view('main', compact('open_registrations', 'ongoing_competitions', 'recent_competitions'));
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
     * Confirm the results
     *
     * @param MatchLog $matchLog
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function confirmResults(MatchLog $matchLog)
    {
        $this->authorize('confirmRejectResults', $matchLog);
        $matchLog->confirmed = true;
        $matchLog->save();
        return back()->with('success', __('competitions/management.results_confirmed'));
    }

    /**
     * Reject the result and roll back the changes.
     *
     * @param MatchLog $matchLog
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function rejectResults(MatchLog $matchLog)
    {
        $this->authorize('confirmRejectResults', $matchLog);
        $matchLog->reject();
        return back()->with('success', __('competitions/management.results_deleted'));
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
