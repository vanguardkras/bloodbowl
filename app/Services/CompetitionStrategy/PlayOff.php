<?php


namespace App\Services\CompetitionStrategy;


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
        // Find max round
        // If not max create automatically using makePlayOffOrder()
    }

    /**
     * Get the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    protected function checkRequiredCurrentRoundMatches(): bool
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
        // TODO: Implement createTrophies() method.
    }

    /**
     * Get the max round for the competition.
     *
     * @return int
     */
    protected function maxRound(): int
    {
        $this->competition->loadCount('teams');
        for ($i = $this->competition->teams_count; $i <= 65536; $i++) {
            if (($i & ($i - 1)) === 0) {
                return log10($i) / log10(2);
            }
        }
    }
}
