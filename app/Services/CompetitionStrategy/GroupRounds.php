<?php


namespace App\Services\CompetitionStrategy;


class GroupRounds extends Type
{
    /**
     * Validation rules for the parameters and parameters themselves.
     *
     * @var array
     */
    protected $validationRules = [
        'groups_size' => 'required|integer|min:2|max:512',
        'group_rounds_wo_po' => 'boolean',
        'group_rounds_play_off' => 'required|in:2,4,8,16,32,64',
    ];

    /**
     * Change competition instance parameters.
     */
    public function fillParameters()
    {
        $parameters = new \StdClass;
        $parameters->groups_size =  request()->groups_size;
        $parameters->group_rounds_play_off =  request()->group_rounds_wo_po
            ? 0 : request()->group_rounds_play_off;
        $this->competition->parameters = $parameters;
    }

    /**
     * Start the next competition round.
     */
    public function nextRound()
    {
        // Find max round
        // If not max round increment the round
        // Create new records in scores table if the first or po
        // If round = 1 no need to make order
        // If round > 1 make order for po taking number of player from settings
        // If the last round find the winners
        // Autocreate bots
    }

    /**
     * Check the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    protected function checkRequiredCurrentRoundMatches(): bool
    {
        $groups_size = $this->competition->parameters->groups_size;
        $max_round_before_po = $groups_size - 1;
        if ($this->competition->round > $max_round_before_po) {
            $teams_in_round = $this->competition
                ->scores()
                ->where('round', $this->competition->round)
                ->count();
            $required_matches = $teams_in_round / 2;
        } else {
            $total_teams = $this->competition->teams()->count();
            $required_matches = ($total_teams / $groups_size) * ($groups_size - 1);
        }
        return $required_matches === $this->competition->getCurrentRoundPlayedMatches();
    }

    /**
     * Get the max round for the competition.
     *
     * @return int
     */
    protected function maxRound(): int
    {
        $max = $this->competition->parameters->groups_size - 1;
        if ($this->competition->parameters->group_rounds_play_off) {
            $max += log10($this->competition->parameters->group_rounds_play_off) / log10(2);
        }
        return $max;
    }
}
