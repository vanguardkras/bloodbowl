<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitionCreateRequest;
use App\Models\Competition;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('competitions.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('competitions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Gumlet\ImageResizeException
     * @throws \ReflectionException
     * @throws \App\Services\CompetitionStrategy\CompetitionStrategyException
     */
    public function store(CompetitionCreateRequest $request)
    {
        $competition = new Competition($request->all());
        $competition->setStrategy();
        $competition->fillParameters();
        $competition->user_id = auth()->user()->id;
        $competition->max_teams = $request->filled('any_max_teams') ? $request->max_teams : 0;

        if ($request->hasFile('logo')) {
            $competition->logo = ImageUploader::store('logo', 'competitions_logo');
        }

        $competition->save();

        $competition->races()->sync(array_values($request->races));
        return redirect('/competitions/' . $competition->id);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\Response
     */
    public function show(Competition $competition)
    {
        dd($competition->parameters);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\Response
     */
    public function edit(Competition $competition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Competition $competition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Competition $competition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competition $competition)
    {
        //
    }
}
