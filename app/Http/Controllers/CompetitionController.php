<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitionSaveRequest;
use App\Models\Competition;
use App\Models\Team;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Competition::class, 'competition');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $competitions = auth()->user()->competitions;
        return view('competitions.list', compact('competitions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $types = competitionTypes();
        return view('competitions.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Gumlet\ImageResizeException
     * @throws \ReflectionException
     */
    public function store(CompetitionSaveRequest $request)
    {
        $competition = new Competition($request->all());
        $competition->fillParameters();
        $competition->user_id = auth()->user()->id;
        $competition->max_teams = $request->filled('any_max_teams') ? 0 : $request->max_teams;

        if ($request->hasFile('logo')) {
            $competition->logo = ImageUploader::store('logo', 'competitions_logo');
        }

        $competition->save();

        $competition->races()->sync(array_values($request->races));
        return redirect('/competitions/' . $competition->id . '/edit')
            ->with('success', __(
                'competitions/create.success_message',
                ['name' => $competition->name]
            ));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Competition $competition)
    {
        $competition->load('registeredTeams.user', 'teams.user');
        $competition->setStrategy();
        dd($competition->strategy->makePlayOffOrder());
        return view('competitions.show', compact('competition'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Competition $competition)
    {
        if ($competition->round) {
            return back()->with('alert', __('competitions/list.edit_fail'));
        }

        $competitionRaces = $competition->races;

        return view('competitions.edit', compact(['competition', 'competitionRaces']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\RedirectResponse
     * @throws \ReflectionException
     * @throws \Gumlet\ImageResizeException
     */
    public function update(CompetitionSaveRequest $request, Competition $competition)
    {
        $competition->fill($request->all());
        $competition->max_teams = $request->filled('any_max_teams') ? 0 : $request->max_teams;
        $competition->fillParameters();

        if ($request->hasFile('logo')) {
            $competition->logo = ImageUploader::store('logo', 'competitions_logo', $competition->logo);
        }

        $competition->save();
        $competition->races()->sync(array_values($request->races));

        return back()->with('success', __('competitions/create.success_edit_message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Competition $competition)
    {
        if ($competition->round) {
            return back()->with('alert', __('competitions/list.delete_fail'));
        }

        $name = $competition->name;
        $competition->delete();
        return redirect('/competitions')->with('success', __(
            'competitions/list.delete_success',
            ['name' => $name]
        ));
    }


    /**
     * Start the next round of the competition.
     *
     * @param Competition $competition
     * @return \Illuminate\Http\RedirectResponse
     * @throws \ReflectionException
     */
    public function nextRound(Competition $competition)
    {
        $competition->nextRound();
        return back();
    }

    /**
     * Approve/reject a team registration.
     *
     * @param Request $request
     * @param Competition $competition
     * @param Team $team
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function registerTeam(Request $request, Competition $competition, Team $team)
    {
        $this->authorize('registerTeamCommissioner', $competition);

        if ($request->action === 'approve') {
            $team->competition_id = $competition->id;
            $team->save();
        }

        $competition->registeredTeams()->detach($team);

        return back();
    }
}
