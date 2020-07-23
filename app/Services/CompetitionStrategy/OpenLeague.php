<?php


namespace App\Services\CompetitionStrategy;

use App\Models\Score;
use App\Models\Team;
use App\Models\Trophy;

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
     * Get list of possible opponents for the team.
     *
     * @param Team $team
     * @return mixed
     */
    public function getPossibleOpponents(Team $team)
    {
        if ($this->competition->round === 1) {
            // Open league games stage

            // Check if already has max games
            if ($this->competition->parameters->max_games) {
                $number_of_games = $this->competition->matchLogs()
                    ->where('team_id_1', $team->id)
                    ->orWhere('team_id_2', $team->id)
                    ->count();
                if ($number_of_games >= $this->competition->parameters->max_games) {
                    return [];
                }
            }

            $results = $this->competition->scores()
                ->where('team_id', '!=', $team->id)
                ->where('round', 1)
                ->get()->pluck('team_id')->toArray();

            $exclude = [];

            // Check if the last game was against the same team
            if (!$this->competition->parameters->one_team_row) {
                $last_game = $this->competition->matchLogs()
                    ->where('round', 1)
                    ->where(function ($query) use ($team) {
                        $query->where('team_id_1', $team->id)
                            ->orWHere('team_id_2', $team->id);
                    })->latest()->first();

                if ($last_game) {
                    $exclude[] = $last_game->team_id_1 === $team->id ?
                        $last_game->team_id_2 :
                        $last_game->team_id_1;
                }

                foreach ($results as $result) {
                    $last_game_other = $this->competition->matchLogs()
                        ->where('round', 1)
                        ->where(function ($query) use ($result) {
                            $query->where('team_id_1', $result)
                                ->orWHere('team_id_2', $result);
                        })->latest()->first();

                    if ($last_game_other) {
                        $other_opponent = $last_game_other->team_id_1 === $result ?
                            $last_game_other->team_id_2 :
                            $last_game_other->team_id_1;

                        if ($other_opponent === $team->id) {
                            $exclude[] = $result;
                        }
                    }
                }
            }

            // Check if the maximum games against the same team is reached
            if ($this->competition->parameters->max_one_team_games) {
                foreach ($results as $result) {
                    $games_number = $this->competition->matchLogs()
                        ->where('round', 1)
                        ->where(function ($query) use ($team, $result) {
                            $query->where(function ($query) use ($team, $result) {
                                $query->where('team_id_1', $team->id)
                                    ->where('team_id_2', $result);
                            })->orWhere(function ($query) use ($team, $result) {
                                $query->where('team_id_2', $team->id)
                                    ->where('team_id_1', $result);
                            });
                        })->count();

                    if ($games_number >= $this->competition->parameters->max_one_team_games) {
                        $exclude[] = $result;
                    }
                }
            }

            $results = array_values(array_diff($results, $exclude));
            return $results;

        } elseif ($this->competition->round > 1) {
            // In case of PO
            return [$this->getPlayOffOpponent($team->id)];
        }

        return [];
    }

    /**
     * Get scores data for informational pages
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getScores()
    {
        return $this->competition->scores()
            ->where('round', '=', 1)
            ->orderBy('order')
            ->with('team.user')
            ->get();
    }

    /**
     * Start the next competition round.
     */
    public function nextRound()
    {
        if ($this->competition->round >= $this->maxRound()) { // Last round
            $this->makePlayOffOrder();
            $this->finish();
            session()->flash('success', __('competitions/management.finish_success'));

        } elseif ($this->competition->round === 0) { // First round
            foreach ($this->competition->teams as $team) {
                $score = new Score();
                $score->competition_id = $this->competition->id;
                $score->team_id = $team->id;
                $score->round = 1;
                $score->save();
            }
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.start') . ' ' . $this->competition->name);

        } elseif ($this->competition->round === 1) { // End of the main phase
            $this->makePlayOffOrder($this->competition->parameters->open_league_play_off);
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
        } else {
            $this->makePlayOffOrder();
            $this->competition->round++;
            $this->competition->save();
            session()->flash('success', __('competitions/management.next_success'));
        }
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

    /**
     * Get the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    public function checkRequiredCurrentRoundMatches(): bool
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
     * Record new match results for the competition.
     *
     * @param array $results
     * @return mixed
     * @throws \ReflectionException
     */
    public function recordResults(array $results)
    {
        if ($this->competition->round === 1) {
            // Check if it wasn't the last game of the team against the same team
            if (!$this->competition->parameters->one_team_row) {
                $team_1_last_log = $this->competition->matchLogs()
                    ->where('team_id_1', $results['team_1'])
                    ->orWhere('team_id_2', $results['team_1'])
                    ->latest()
                    ->first();
                $team_2_last_log = $this->competition->matchLogs()
                    ->where('team_id_1', $results['team_2'])
                    ->orWhere('team_id_2', $results['team_2'])
                    ->latest()
                    ->first();

                if (isset($team_1_last_log) && isset($team_2_last_log)) {
                    if (
                        (($team_1_last_log->team_id_1 == $results['team_1'] && $team_1_last_log->team_id_2 == $results['team_2']) ||
                            ($team_1_last_log->team_id_1 == $results['team_2'] && $team_1_last_log->team_id_2 == $results['team_1'])) ||
                        (($team_2_last_log->team_id_1 == $results['team_1'] && $team_2_last_log->team_id_2 == $results['team_2']) ||
                            ($team_2_last_log->team_id_1 == $results['team_2'] && $team_2_last_log->team_id_2 == $results['team_1']))
                    ) {
                        return back()->with('alert', __('competitions/management.league_same_team_last_error'));
                    }
                }
            }

            // Check if the teams have reached their maximum games number
            if ($this->competition->parameters->max_one_team_games) {
                $team_1_games = $this->competition->matchLogs()
                    ->where('team_id_1', $results['team_1'])
                    ->orWhere('team_id_2', $results['team_1'])
                    ->count();

                $team_2_games = $this->competition->matchLogs()
                    ->where('team_id_1', $results['team_2'])
                    ->orWhere('team_id_2', $results['team_2'])
                    ->count();

                if (
                    $team_1_games >= $this->competition->parameters->max_games ||
                    $team_2_games >= $this->competition->parameters->max_games
                ) {
                    return back()->with('alert', __('competitions/management.max_games_error'));
                }
            }

            // Check if the teams have already played maximum games against each other
            if ($this->competition->parameters->max_one_team_games) {
                $games = $this->competition->matchLogs()
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

                if ($games >= $this->competition->parameters->max_one_team_games) {
                    return back()->with('alert', __('competitions/management.max_games_against_error'));
                }
            }

            // Get scores of the current teams
            $scores = $this->competition->scores()
                ->where(function ($query) use ($results) {
                    $query->where('team_id', $results['team_1'])
                        ->orWhere('team_id', $results['team_2']);
                })
                ->get()
                ->keyBy('team_id');

            foreach ($this->formatResults($results) as $key => $result) {
                $scores[$key]->touchdowns += $result['touchdowns'];
                $scores[$key]->touchdowns_diff += $result['touchdowns_diff'];
                $scores[$key]->score += $result['points'];
                $scores[$key]->save();
            }

            $this->createMatchLogAndHistory($results, 1);

            return back()->with('success', __('competitions/management.save_success'));

        } elseif ($this->competition->round <= $this->competition->getMaxRound()) {
            $this->recordPlayOffResults($results);
        }

    }

    /**
     * Create trophies for a finished competition.
     */
    protected function createTrophies()
    {
        $po_teams = $this->competition->parameters->open_league_play_off;

        if ($this->competition->tops_number <= $po_teams) {
            $this->createPlayOffTrophies();
        } else {
            $this->createPlayOffTrophies($po_teams);
            $scores = $this->competition->scores()
                ->where('round', 1)
                ->orderBy('score', 'DESC')
                ->orderBy('touchdowns_diff', 'DESC')
                ->orderBy('touchdowns', 'DESC')
                ->offset($po_teams)
                ->limit($this->competition->tops_number - $po_teams)
                ->get();

            $position = $po_teams + 1;

            foreach ($scores as $score) {
                $trophy = new Trophy;
                $trophy->competition_id = $this->competition->id;
                $trophy->team_id = $score->team_id;
                $trophy->position = $position++;
                $trophy->save();
            }

        }
    }

    /**
     * Get current competition first round of play off
     *
     * @return mixed
     */
    protected function getPlayOffStartRound()
    {
        return 2;
    }

    /**
     * Get current competition number of play off players.
     *
     * @return mixed|void
     */
    protected function getPlayOffTeamsNumber()
    {
        return $this->competition->parameters->open_league_play_off;
    }
}
