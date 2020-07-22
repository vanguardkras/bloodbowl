<?php


namespace App\Services\CompetitionStrategy;


use App\Models\Score;
use App\Models\Team;

class PlayOff extends Type
{
    /**
     * Change competition instance parameters.
     */
    public function fillParameters()
    {
        // This competition type does not contain any parameters.
        // Leave this method empty.
    }

    /**
     * Start the next competition round.
     */
    public function nextRound()
    {
        if ($this->competition->round) {

            $this->makePlayOffOrder();

            if ($this->competition->round == $this->maxRound()) {
                $this->finish();
                session()->flash('success', __('competitions/management.finish_success'));
            } else {
                $this->competition->round++;
                $this->competition->save();
                session()->flash('success', __('competitions/management.next_success'));
            }

        } else {
            $this->firstRoundDistribution();
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.start') . ' ' . $this->competition->name);
        }
    }

    /**
     * Get current competition number of play off players.
     *
     * @return mixed
     */
    protected function getPlayOffTeamsNumber()
    {
        return pow(2, $this->maxRound());
    }

    /**
     * Get the max round for the competition.
     *
     * @return int
     */
    public function maxRound(): int
    {
        $teams = $this->competition->scores()->where('round', 1)->count();
        return intval(ceil(log10($teams) / log10(2)));
    }

    /**
     * Record new match results for the competition.
     *
     * @param array $results
     * @return mixed
     * @throws \ReflectionException
     */
    public function recordResults(array $results)
    {
        $this->recordPlayOffResults($results);
    }

    /**
     * Get the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    public function checkRequiredCurrentRoundMatches(): bool
    {
        $teams_in_round = $this->competition
            ->scores()
            ->where('round', $this->competition->round)
            ->count();
        return ($teams_in_round / 2) === $this->competition->getCurrentRoundPlayedMatches();
    }

    /**
     * Writes data about the competition winners.
     *
     * @param bool $tops_number
     * @return mixed|void
     */
    protected function createTrophies($tops_number = false)
    {
        $this->createPlayOffTrophies();
    }

    /**
     * Get current competition first round of play off
     *
     * @return mixed
     */
    protected function getPlayOffStartRound()
    {
        return 1;
    }

    /**
     * Initial random registered players distribution and adding a necessary number of bots.
     */
    private function firstRoundDistribution()
    {
        $participants = $this->competition->teams()->pluck('id')->shuffle();
        $max_round = intval(ceil(log10($participants->count()) / log10(2)));
        $total = pow(2, $max_round);
        $bots_number = $total - $participants->count();

        for ($i = 0; $i < $bots_number; $i++) {
            $races = races();
            $bot = new Team();
            $bot->user_id = 1;
            $bot->name = 'BOT';
            $bot->race_id = $races[rand(0, count($races) - 1)]->id;
            $bot->competition_id = $this->competition->id;
            $bot->save();
            $participants->push($bot->id);
        }

        foreach ($participants as $order => $team_id) {
            $score = new Score();
            $score->competition_id = $this->competition->id;
            $score->round = 1;
            $score->team_id = $team_id;
            $score->order = $order;
            $score->save();
        }
    }

}
