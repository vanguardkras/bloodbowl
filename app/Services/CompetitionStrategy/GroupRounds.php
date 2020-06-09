<?php


namespace App\Services\CompetitionStrategy;


use App\Models\MatchLog;
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
        'groups_size' => 'required|in:2,4,6,8,10,12,14,16,18,20',
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

    public function getGroupStageTeams()
    {
        $max_round = $this->competition->parameters->groups_size - 1;
        return $this->competition->scores()
            ->where('round', '>=', 0)
            ->where('round', '<=', $max_round)
            ->orderBy('order')
            ->with('team.user')
            ->get();
    }

    /**
     * Start the next competition round.
     */
    public function nextRound()
    {
        if (!$this->competition->round) {
            $this->firstRoundDistribution();
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.start') . ' ' . $this->competition->name);
        } elseif ($this->competition->round >= $this->maxRound()) {
            if ($this->checkRequiredCurrentRoundMatches()) {
                $this->finish();
                session()->flash('success', __('competitions/management.finish_success'));
            } else {
                session()->flash('alert', __('competitions/management.finish_error'));
            }
        } elseif ($this->competition->round == ($this->competition->parameters->groups_size - 1)) {
            $finalists = $this->getTopsFromGroups($this->competition->parameters->group_rounds_play_off);
            $order = 0;
            foreach ($finalists as $finalist) {
                $score = new Score;
                $score->competition_id = $this->competition->id;
                $score->round = $this->competition->round + 1;
                $score->team_id = $finalist->team_id;
                $score->order = $order;
                $score->save();
                $order++;
            }
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
        } elseif ($this->competition->round < ($this->competition->parameters->groups_size - 1)) {
            if ($this->checkRequiredCurrentRoundMatches()) {
                $this->competition->round++;
                $this->competition->save();
                session()->flash('success', __('competitions/management.next_success'));
            } else {
                session()->flash('alert', __('competitions/management.next_error'));
            }
        } elseif ($this->competition->round >= $this->competition->parameters->groups_size) {
            $this->makePlayOffOrder();
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
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
        } else {
            $teams_in_round = $this->competition->teams()->count();
        }
        $required_matches = intval($teams_in_round / 2);

        return $required_matches === $this->competition->getCurrentRoundPlayedMatches();
    }

    /**
     * Create trophies for a finished competition.
     *
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
    public function maxRound(): int
    {
        $max = $this->competition->parameters->groups_size - 1;
        if ($this->competition->parameters->group_rounds_play_off) {
            $max += log10($this->competition->parameters->group_rounds_play_off) / log10(2);
        }
        return $max;
    }

    /**
     * Record new match results for the competition.
     *
     * @param array $results
     * @return mixed
     */
    public function recordResults(array $results)
    {
        //return back()->with('alert', 'It is not gonna work');

        //TODO: Check if it is PO and select either from groups or grom PO
        $scores = $this->competition->scores()
            ->where('team_id', $results['team_1'])
            ->orWhere('team_id', $results['team_2'])
            ->get();

        if ($scores->count() !== 2 || $scores[0]->round !== $scores[1]->round) {
            return back()->with('alert', 'Something went wrong. Contact with the administrator.');
        }

        if ($this->competition->round <= $this->competition->parameters->groups_size) {
            //TODO: in case of groups, check if the teams are from the same group
        } else {
            // TODO: in case of PO check that teams play against each other.
        }

        //$this->createMatchLogAndHistory($results);
        back()->with('success', 'The results have been successfully saved.');
    }

    /**
     * Get current competition number of play off rounds.
     *
     * @return mixed|void
     */
    protected function getPlayOffTeamsNumber()
    {
        return $this->competition->parameters->group_rounds_play_off;
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
    private function getTopsFromGroups($number)
    {
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
