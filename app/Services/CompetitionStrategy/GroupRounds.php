<?php


namespace App\Services\CompetitionStrategy;

use App\Models\Score;
use App\Models\Team;
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

    /**
     * Get teams list for the group stage participants
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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
     * Get list of possible opponents for the team.
     *
     * @param Team $team
     * @return mixed
     */
    public function getPossibleOpponents(Team $team)
    {
        $groups_size = $this->competition->parameters->groups_size;

        if ($this->competition->round < $groups_size) {
            //In case of group stage

            $team_score = $this->competition->scores()
                ->where('team_id', $team->id)->first(['round', 'order']);

            $group_number = floor($team_score->order / $groups_size);
            $order_start = $group_number * $groups_size;
            $order_end = $order_start + $groups_size - 1;
            $teams = $this->competition->scores()
                ->where('round', $team_score->round)
                ->where('team_id', '!=', $team->id)
                ->where('order', '>=', $order_start)
                ->where('order', '<=', $order_end)
                ->get();
            $results = $teams->pluck('team_id');
            $results_number = $results->count();

            for ($i = 0; $i < $results_number; $i++) {

                $played = $this->competition->matchLogs()
                    ->where('round', '>=', 1)
                    ->where('round', '<', $groups_size)
                    ->where(function ($query) use ($team, $results, $i) {
                        $query->where(function ($query) use ($team, $results, $i) {
                            $query->where('team_id_1', $team->id)
                                ->where('team_id_2', $results[$i]);
                        })->orWhere(function ($query) use ($team, $results, $i) {
                            $query->where('team_id_2', $team->id)
                                ->where('team_id_1', $results[$i]);
                        });
                    })->get();
                if ($played->isNotEmpty()) {
                    unset($results[$i]);
                }
            }

            return array_values($results->toArray());

        } else {
            // In case of PO
            return [$this->getPlayOffOpponent($team->id)];
        }
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
            $this->makePlayOffOrder();
            $this->finish();
            session()->flash('success', __('competitions/management.finish_success'));
        } elseif ($this->competition->round == ($this->competition->parameters->groups_size - 1)) {
            $number_of_players = $this->competition->parameters->group_rounds_play_off;
            $finalists = $this->getTopsFromGroups($number_of_players);

            $this->createScoresForFirstPlayOffRound($finalists, $number_of_players);

            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
        } elseif ($this->competition->round < ($this->competition->parameters->groups_size - 1)) {
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
        } elseif ($this->competition->round >= $this->competition->parameters->groups_size) {
            $this->makePlayOffOrder();
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
        }
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
        $teams = $this->competition->teams()->inRandomOrder()->get();
        $registered_teams = $teams->count();
        $groups_size = $this->competition->parameters->groups_size;
        $groups_number = ceil($registered_teams / $groups_size);
        $bots_number = $groups_number * $groups_size - $registered_teams;
        $minimum_bots = floor($bots_number / $groups_number);
        $how_many_groups_add_bot = $bots_number % $groups_number;

        $order = 0;
        $index = 0;

        for ($i = 0; $i < $groups_number * $groups_size; $i++) {
            $score = new Score;
            $score->competition_id = $this->competition->id;
            $score->order = $order;


            $group_number_from_end = $groups_number - floor($order / $groups_size);
            $number_of_bots = $minimum_bots + ($group_number_from_end <= $how_many_groups_add_bot ? 1 : 0);
            $current_number_in_group_from_end = $groups_size - ($order % $groups_size);
            $is_bot = $current_number_in_group_from_end <= $number_of_bots;


            if ($is_bot) {
                $races = races();
                $bot = new Team();
                $bot->user_id = 1;
                $bot->name = 'BOT';
                $bot->race_id = $races[rand(0, count($races) - 1)]->id;
                $bot->competition_id = $this->competition->id;
                $bot->save();
                $score->team_id = $bot->id;
            } else {
                $score->team_id = $teams[$index]->id;
                $index++;
            }

            $score->save();
            $order++;
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

    /**
     * Check the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    public function checkRequiredCurrentRoundMatches(): bool
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
     */
    public function createTrophies()
    {
        $po_teams = $this->competition->parameters->group_rounds_play_off;

        if ($this->competition->tops_number <= $po_teams) {
            $this->createPlayOffTrophies();
        } else {
            $po_trophied_teams = $this->createPlayOffTrophies($po_teams);
            $scores = $this->getTopsFromGroups($this->competition->tops_number);

            $position = $po_teams + 1;

            foreach ($scores as $score) {
                if ($po_trophied_teams->contains($score->team_id)) {
                    continue;
                }
                $trophy = new Trophy;
                $trophy->competition_id = $this->competition->id;
                $trophy->team_id = $score->team_id;
                $trophy->position = $position++;
                $trophy->save();
            }

        }
    }

    /**
     * Record new match results for the competition.
     *
     * @param array $results
     * @return mixed
     */
    public function recordResults(array $results)
    {
        // In case of the groups stage
        if ($this->competition->round < $this->competition->parameters->groups_size) {

            // Get scores of the current teams
            $scores = $this->competition->scores()
                ->where('round', '<', $this->competition->parameters->groups_size)
                ->where(function ($query) use ($results) {
                    $query->where('team_id', $results['team_1'])
                        ->orWhere('team_id', $results['team_2']);
                })
                ->get()
                ->keyBy('team_id');

            $group_1 = floor($scores[$results['team_1']]->order / $this->competition->parameters->groups_size);
            $group_2 = floor($scores[$results['team_2']]->order / $this->competition->parameters->groups_size);

            // Check that teams can play against each other and are on the same stage.
            $played = $this->competition->matchLogs()
                ->where('round', '<', $this->competition->parameters->groups_size)
                ->where(function ($query) use ($results) {
                    $query
                        ->where(function ($query) use ($results) {
                            $query->where('team_id_1', $results['team_1'])
                                ->where('team_id_2', $results['team_2']);
                        })
                        ->orWhere(function ($query) use ($results) {
                            $query->where('team_id_1', $results['team_2'])
                                ->where('team_id_2', $results['team_1']);
                        });
                })
                ->count();

            if ($played) {
                return back()->with('alert', __('competitions/management.already_played_error'));
            }
            if ($group_1 !== $group_2 || $scores[$results['team_1']]->round !== $scores[$results['team_2']]->round) {
                return back()->with('alert', __('competitions/management.different_groups_error'));
            }

            // Update database data
            foreach ($this->formatResults($results) as $key => $result) {
                $scores[$key]->touchdowns += $result['touchdowns'];
                $scores[$key]->touchdowns_diff += $result['touchdowns_diff'];
                $scores[$key]->score += $result['points'];
                $scores[$key]->round++;
                $scores[$key]->save();
            }

            $this->createMatchLogAndHistory($results, $scores[$results['team_1']]->round);

            // Check if it is the last match to start the next round
            if ($this->competition->round < $this->competition->parameters->groups_size - 1) {
                $currentRoundScoresCount = $this->competition->scores()
                    ->where('round', '>=', $this->competition->round)
                    ->count();

                if ($currentRoundScoresCount === $this->competition->teams()->count()) {
                    $this->competition->round++;
                    $this->competition->save();
                }
            }

        } elseif ($this->competition->parameters->group_rounds_play_off) {

            $this->recordPlayOffResults($results);

        } else {
            return back()->with('alert', __('competitions/management.no_play_off_error'));
        }

        return back()->with('success', __('competitions/management.save_success'));
    }

    /**
     * Get current competition first round of play off
     *
     * @return mixed
     */
    protected function getPlayOffStartRound()
    {
        return $this->competition->parameters->groups_size;
    }

    /**
     * Get current competition number of play off players.
     *
     * @return mixed|void
     */
    protected function getPlayOffTeamsNumber()
    {
        return $this->competition->parameters->group_rounds_play_off;
    }
}
