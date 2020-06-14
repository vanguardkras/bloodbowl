<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamCreateRequest;
use App\Models\Race;
use App\Models\Team;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Team::class, 'team');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $teams = auth()->user()->teams;

        return view('teams.list', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $teamsCount = auth()->user()->loadCount('teams')->teams_count;

        if ($teamsCount >= config('app.max_teams')) {
            return back()
                ->with('alert', __(
                    'teams.count_limit_msg',
                    ['max' => config('app.max_teams')]
                ));
        }

        $races = Race::getAllByLocale();

        return view('teams.create', compact('races'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TeamCreateRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Gumlet\ImageResizeException
     */
    public function store(TeamCreateRequest $request)
    {
        $team = new Team;
        $team->user_id = auth()->user()->id;
        $team->name = $request->name;
        $team->race_id = $request->race_id;

        if ($request->hasFile('logo')) {
            $team->logo = ImageUploader::store('logo', 'teams_logo');
        }

        $team->save();

        return redirect('/teams')
            ->with('success', __(
                'teams.successful_create',
                ['name' => $team->name]
            ));
    }

    /**
     * Display the specified resource.
     *
     * @param Team $team
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Team $team)
    {
        $history = $team->history();
        return view('teams.show', compact('team', 'history'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Team $team
     * @return string
     * @throws \Gumlet\ImageResizeException
     */
    public function update(Request $request, Team $team)
    {
        $team->logo = ImageUploader::store('logo', 'teams_logo', $team->logo);
        $team->save();

        return $team->logo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Team $team
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Team $team)
    {
        if ($team->competition_id) {
            return back()->with('alert', __(
                'teams.delete_error_msg',
                ['name' => $team->name]
            ));
        }

        $name = $team->name;

        if($team->logo) {
            Storage::disk('public')->delete($team->logo);
        }

        $team->delete();

        return back()->with('success', __(
            'teams.delete_success_msg',
            ['name' => $name]
        ));
    }
}
