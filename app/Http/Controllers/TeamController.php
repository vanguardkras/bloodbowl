<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamCreateRequest;
use App\Models\Race;
use App\Models\Team;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Team::class, 'team');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = auth()->user()->teams;

        return view('teams.list', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
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
     * @param \App\Team $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        return view('teams.show', compact('team'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Team $team
     * @return \Illuminate\Http\Response
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
     * @param \App\Team $team
     * @return \Illuminate\Http\Response
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
        $team->delete();
        return back()->with('success', __(
            'teams.delete_success_msg',
            ['name' => $name]
        ));
    }
}
