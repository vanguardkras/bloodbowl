<?php


namespace App\Services\CompetitionStrategy;


class OpenLeague extends Type
{
    /**
     * Validation rules for the parameters and parameters themselves.
     *
     * @var array
     */
    protected $validationRules = [
        'max_games' => 'required|integer|min:1|max:1024',
        'any_max_games' => 'boolean',
        'max_one_team_games' => 'required|integer|min:1|max:1024',
        'any_max_one_team_games' => 'boolean',
        'one_team_row' => 'boolean',
        'open_league_play_off' => 'required|in:2,4,8,16,32,64',
        'open_league_wo_po' => 'boolean',
    ];

    /**
     * Change competition instance parameters.
     */
    public function fillParameters()
    {
        $parameters = new \StdClass;
        $parameters->max_games =  request()->any_max_games
            ? 0 : request()->max_games;
        $parameters->max_one_team_games =  request()->any_max_one_team_games
            ? 0 : request()->max_one_team_games;
        $parameters->one_team_row = request()->one_team_row
            ? true : false;
        $parameters->open_league_play_off = request()->open_league_wo_po
            ? 0 : request()->open_league_play_off;
        $this->competition->parameters = $parameters;
    }
}
