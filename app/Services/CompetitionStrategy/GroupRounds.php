<?php


namespace App\Services\CompetitionStrategy;


use App\Models\Score;
use App\Models\Trophy;
use Illuminate\Support\Facades\DB;

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
        $parameters->groups_size = request()->groups_size;
        $parameters->group_rounds_play_off = request()->group_rounds_wo_po
            ? 0 : request()->group_rounds_play_off;
        $this->competition->parameters = $parameters;
    }

    /**
     * Start the next competition round.
     */
    public function nextRound()
    {
        if (!$this->competition->round) {
            $this->firstRoundDistribution();
            session()->flash('success', 'You have successfully started competition ' . $this->competition->name);
        } elseif ($this->competition->round >= $this->maxRound()) {
            if ($this->checkRequiredCurrentRoundMatches()) {
                $this->finish();
            } else {
                session()->flash('alert', 'You cannot close the competitions while not all the matches are finished');
            }
        } elseif ($this->competition->round == ($this->competition->parameters->groups_size - 1)) {
            // TODO: Select top x from each group.
        }
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
     * Create trophies for a finished competition.
     * @param bool $tops_number
     */
    public function createTrophies($tops_number = false)
    {
        $po_teams = $this->competition->parameters->group_rounds_play_off;

        if ($this->competition->tops_number <= $po_teams) {
            $this->createPlayOffTrophies();
        } else {
            $po_trophied_teams = $this->createPlayOffTrophies($po_teams);

            $scores = $this->getTopsFromGroups($this->competition->tops_number);

            $position = $po_teams + 1;
            foreach ($scores as $score) {
                if (!$po_trophied_teams->contains('team_id', $score->team_id)) {
                    $trophy = new Trophy;
                    $trophy->competition_id = $this->competition->id;
                    $trophy->team_id = $score->team_id;
                    $trophy->position = $position;
                    $trophy->save();
                    $position++;
                }
            }

        }
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

    /**
     * Make the initial teams RANDOM distribution.
     *
     * The principle of the distribution:
     * 1) Shuffle all teams.
     * 2) Find the number of group basing on the group size rounded to the ceil.
     *     Example: 13 teams with groups_size = 4. The number of groups - 4.
     * 3) Find the number of bots required to fill all the teams.
     *     Example: 13 teams with groups_size = 4. Number of bots = 3.
     * 4) Assign each team to a group with order parameter. The last
     * groups have ONE bot team (if exist).
     */
    private function firstRoundDistribution()
    {
        $registered_teams = $this->competition->teams()->count();
        $groups_size = $this->competition->parameters->groups_size;
        $groups_number = ceil($registered_teams / $groups_size);
        $bots_number = $groups_number - $registered_teams;

        $order = 0;
        foreach ($this->competition->teams()->inRandomOrder()->get() as $team) {
            $score = new Score;
            $score->competition_id = $this->competition->id;

            $is_bot = (floor($order / $groups_size) >= ($groups_number - $bots_number)) &&
                (($order % $groups_size) === ($groups_size - 1));

            $score->team_id = $is_bot ? 1 : $team->id;
            $score->order = $order;
            $score->save();
            $order++;
        }
    }

    /**
     * Get top teams from groups using the next algorithm:
     * 1) Get all teams in groups before the Play Off and sort them by a position
     * inside their groups
     * 2) Group all teams with the same position in different groups and sort them
     * by their score
     * 3) Get only neccessary number of teams.
     *
     * @param $number
     * @return \Illuminate\Support\Collection
     */
    private function getTopsFromGroups($number) {
        $groups_size = $this->competition->parameters->groups_size;
        $groups_number = ceil($this->competition->teams()->count() / $groups_size);

        $scores = $this->competition->scores()
            ->addSelect(DB::raw('*, FLOOR(`scores`.`order` / ' . $groups_size . ') as `group`'))
            ->where('round', '<', $groups_size)
            ->orderBy('group')
            ->orderBy('score', 'DESC')
            ->orderBy('touchdowns_diff', 'DESC')
            ->orderBy('touchdowns', 'DESC')
            ->get();

        $tops_scores = [];

        // Form sub-arrays of teams by a place
        for ($position = 0; $position < $groups_size; $position++) {
            for ($group = 0; $group < $groups_number; $group++) {
                $tops_scores[$position][] = $scores[$group * $groups_size + $position];
            }
        }

        // Sort sub-arrays in order score, touchdown_diff, touchdowns
        array_walk($tops_scores, function (&$score) {
            uasort($score, function ($a, $b) {
                if ($a->score === $b->score) {
                    if ($b->touchdowns_diff === $a->touchdowns_diff) {
                        return $b->touchdowns <=> $a->touchdowns;
                    }
                    return $b->touchdowns_diff <=> $a->touchdowns_diff;
                }
                return $b->score <=> $a->score;
            });
        });

        $tops_scores = array_merge(... $tops_scores);
        return collect(array_slice($tops_scores, 0, $number));
    }
}
