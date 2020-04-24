<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Race;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $races = Race::all();

        return view('super.races', compact('races'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('/races');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $race = new Race;
        $race->name_en = $request->name_en;
        $race->name_ru = $request->name_ru;
        $race->is_default = $request->has('is_default') ? true : false;
        $race->save();
        return back()->with('message', 'A race has been successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function show(Race $race)
    {
        return redirect('/races');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function edit(Race $race)
    {
        return redirect('/races');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Race $race)
    {
        $message = '';

        $race->name_en = $request->name_en;
        $race->name_ru = $request->name_ru;
        $race->is_default = $request->has('is_default') ? true : false;
        if ($race->isDirty()) {
            $race->save();
            $message = 'The race has been successfully edited';
        }
        return back()->with('message', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Race $race
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Race $race)
    {
        $race->delete();
        return back()->with('message', 'The race has been successfully deleted');
    }
}
