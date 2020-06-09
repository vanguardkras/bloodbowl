<?php


namespace App\Services\CompetitionStrategy;


class OpenLeague extends Type
{
    /**
     * Round after which the registration is forbidden.
     *
     * @var int
     */
    protected $registrationMaxRound = 1;

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
        $parameters->max_games = request()->any_max_games
            ? 0 : request()->max_games;
        $parameters->max_one_team_games = request()->any_max_one_team_games
            ? 0 : request()->max_one_team_games;
        $parameters->one_team_row = request()->one_team_row
            ? true : false;
        $parameters->open_league_play_off = request()->open_league_wo_po
            ? 0 : request()->open_league_play_off;
        $this->competition->parameters = $parameters;
    }

    /**
     * Start the next competition round.
     */
    public function nextRound()
    {
        // Find max round
        // If not max round increment the round
        // Create new records in scores table if the first round
        // If po create automatically using makePlayOffOrder()
    }

    /**
     * Get the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    protected function checkRequiredCurrentRoundMatches(): bool
    {
        if ($this->competition->round > 1) {
            $teams_in_round = $this->competition
                ->scores()
                ->where('round', $this->competition->round)
                ->count();
            return ($teams_in_round / 2) === $this->competition->getCurrentRoundPlayedMatches();
        }
        return true;
    }

    /**
     * Writes data about the competition winners.
     *
     * @param bool $tops_number
     * @return mixed|void
     */
    protected function createTrophies($tops_number = false)
    {
        // TODO: Implement createTrophies() method.
    }

    /**
     * Get the max round for the competition.
     *
     * @return int
     */
    public function maxRound(): int
    {
        $max = 1;
        if ($this->competition->parameters->open_league_play_off) {
            $max += log10($this->competition->parameters->open_league_play_off) / log10(2);
        }
        return $max;
    }
}
