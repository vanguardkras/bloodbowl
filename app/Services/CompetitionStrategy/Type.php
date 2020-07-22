<?php


namespace App\Services\CompetitionStrategy;

use App\Models\Competition;
use App\Models\History;
use App\Models\MatchLog;
use App\Models\Score;
use App\Models\Team;
use App\Models\Trophy;
use Illuminate\Support\Facades\Validator;

abstract class Type
{
    /**
     * Competition instance
     *
     * @var Competition
     */
    protected $competition;

    /**
     * Validation rules for the parameters and parameters themselves.
     *
     * @var array
     */
    protected $validationRules = [];

    /**
     * Round after which the registration is forbidden.
     *
     * @var int
     */
    protected $registrationMaxRound = 0;

    public function __construct(Competition $competition, $data)
    {
        Validator::make($data, $this->validationRules);
        $this->competition = $competition;
    }

    /**
     * Make a classic play of view.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function playOffView()
    {
        $playOffRounds = $this->getPlayOffTeamsNumber();
        $lastRound = log10($playOffRounds) / log10(2);
        $startRound = $this->getPlayOffStartRound();
        $scores = $this->competition->scores()
            ->where('round', '>=', $startRound)
            ->with('team.user')
            ->with('team.race')
            ->get();

        return view('competitions.types.common_play_off',
            compact(['playOffRounds', 'lastRound', 'startRound', 'scores']));
    }

    /**
     * Change competition instance parameters.
     */
    public abstract function fillParameters();

    /**
     * Start the next competition round.
     */
    public abstract function nextRound();

    /**
     * Record new match results for the competition.
     * The $result array should be an associative array
     * with the following keys: 'team_1', 'team_2', 'touchdowns_1', 'touchdowns_2'.
     *
     * @param array $results
     * @return mixed
     */
    public abstract function recordResults(array $results);

    /**
     * Competition finishing procedures.
     */
    protected function finish()
    {

        if ($this->competition->round < $this->maxRound() || $this->competition->finished) {
            return back()->with('alert', __('competitions/management.finish_error_general'));
        }

        $this->competition->finished = today()->toDateString();
        $this->competition->save();

        $this->createTrophies();
        $this->competition->matchLogs()->delete();
        $this->competition->teams()->update(['competition_id' => null]);
    }

    /**
     * Creates a classic play of order for the CURRENT round
     * as it is a usual finishing part of many
     * different competition types.
     *
     * @param int $number_of_players use this parameter only if you start the
     * first round of play off.
     */
    protected function makePlayOffOrder(int $number_of_players = 0)
    {
        if ($number_of_players) {
            $current_scores = $this->competition
                ->scores()
                ->where('round', $this->competition->round)
                ->orderBy('score', 'DESC')
                ->orderBy('touchdowns_diff', 'DESC')
                ->orderBy('touchdowns', 'DESC')
                ->limit($number_of_players)
                ->get();
            $this->createScoresForFirstPlayOffRound($current_scores, $number_of_players);
        } else {
            $current_scores = $this->competition
                ->scores()
                ->where('round', $this->competition->round)
                ->orderBy('order')
                ->get();
            for ($i = 0; $i < $current_scores->count() / 2; $i++) {
                $score = new Score;
                $score->competition_id = $this->competition->id;
                $score->round = $this->competition->round + 1;
                $score->team_id = $current_scores[$i * 2]->score > $current_scores[$i * 2 + 1]->score ?
                    $current_scores[$i * 2]->team_id :
                    $current_scores[$i * 2 + 1]->team_id;
                $score->order = $i;
                $score->save();
            }
        }
    }

    /**
     * Create match log and history records.
     *
     * @param array $results
     * @param bool $round
     */
    protected function createMatchLogAndHistory(array $results, $round = false)
    {
        $team_1 = Team::find($results['team_1']);
        $team_2 = Team::find($results['team_2']);

        // Add history
        $history = new History();
        $history->competition_id = $this->competition->id;
        $history->team_id_1 = $results['team_1'];
        $history->team_id_2 = $results['team_2'];
        $history->race_team_1 = $team_1->race_id;
        $history->race_team_2 = $team_2->race_id;
        $history->team_name_1 = $team_1->name;
        $history->team_name_2 = $team_2->name;
        $history->score_1 = $results['touchdowns_1'];
        $history->score_2 = $results['touchdowns_2'];
        $history->date = today()->toDateString();
        $history->save();

        // Add match log
        $mathLog = new MatchLog();
        $mathLog->competition_id = $this->competition->id;
        $mathLog->team_id_1 = $results['team_1'];
        $mathLog->team_id_2 = $results['team_2'];
        $mathLog->score_1 = $results['touchdowns_1'];
        $mathLog->score_2 = $results['touchdowns_2'];
        $mathLog->round = $round ?: $this->competition->round;
        $mathLog->confirmed = $this->competition->self_confirm >= 2;
        $mathLog->date = today()->toDateString();
        $mathLog->history_id = $history->id;
        $mathLog->save();

        // Update teams statistics
        $team_1->touchdowns += $results['touchdowns_1'];
        $team_2->touchdowns += $results['touchdowns_2'];
        $team_1->played++;
        $team_2->played++;
        $team_1->wins += $results['touchdowns_1'] > $results['touchdowns_2'] ? 1 : 0;
        $team_2->wins += $results['touchdowns_2'] > $results['touchdowns_1'] ? 1 : 0;
        $team_1->draws += $results['touchdowns_1'] === $results['touchdowns_2'] ? 1 : 0;
        $team_2->draws += $results['touchdowns_1'] === $results['touchdowns_2'] ? 1 : 0;
        $team_1->save();
        $team_2->save();
    }

    /**
     * Initial play off distribution where the first plays against the last etc.
     *
     * @param $scores
     * @param $number_of_players
     */
    protected function createScoresForFirstPlayOffRound($scores, $number_of_players)
    {
        for ($i = 0; $i < $number_of_players; $i++) {
            $score = new Score;
            $score->competition_id = $this->competition->id;
            $score->round = $this->competition->round + 1;
            $score->team_id = $scores[$i]->team_id;
            $score->order = $i >= ($number_of_players / 2) ?
                (($number_of_players - $i - 1) * 2 + 1) :
                2 * $i;
            $score->save();
        }
    }

    /**
     * Create trophies from a scores collection.
     *
     * @param $team_id
     * @param $position
     */
    protected function createTrophy($team_id, $position)
    {
        $trophy = new Trophy;
        $trophy->competition_id = $this->competition->id;
        $trophy->team_id = $team_id;
        $trophy->position = $position;
        $trophy->save();
    }

    /**
     * Create trophies for a finished competition.
     *
     * @param bool $tops_number
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function createPlayOffTrophies($tops_number = false)
    {
        $team_ids = $this->competition->scores()
            ->orderBy('round', 'DESC')
            ->orderBy('score', 'DESC')
            ->orderBy('touchdowns_diff', 'DESC')
            ->orderBy('touchdowns', 'DESC')
            ->get('team_id')
            ->pluck('team_id')
            ->unique()
            ->take($tops_number ?: $this->competition->tops_number);

        $position = 1;
        foreach ($team_ids as $team_id) {
            $this->createTrophy($team_id, $position);
            $position++;
        }

        return $team_ids;
    }

    /**
     * Format results to an associative array with keys as teams ids
     * and touchdowns, points parameters.
     *
     * @param array $results
     * @return array[]
     */
    protected function formatResults(array $results)
    {
        return [
            intval($results['team_1']) => [
                'touchdowns' => $results['touchdowns_1'],
                'touchdowns_diff' => $results['touchdowns_1'] - $results['touchdowns_2'],
                'points' => $results['touchdowns_1'] > $results['touchdowns_2'] ? $this->competition->winner_points :
                    ($results['touchdowns_1'] === $results['touchdowns_2'] ? 1 : 0),
            ],
            intval($results['team_2']) => [
                'touchdowns' => $results['touchdowns_2'],
                'touchdowns_diff' => $results['touchdowns_2'] - $results['touchdowns_1'],
                'points' => $results['touchdowns_2'] > $results['touchdowns_1'] ? $this->competition->winner_points :
                    ($results['touchdowns_1'] === $results['touchdowns_2'] ? 1 : 0),
            ],
        ];
    }

    /**
     * If you record play off results, use this function for standard checks.
     *
     * @param array $results
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recordPlayOffResults(array $results)
    {
        if ($results['touchdowns_1'] === $results['touchdowns_2']) {
            return back()->with('alert', __('competitions/management.po_equal_results_error'));
        }

        $scores = $this->competition->scores()
            ->where('round', $this->competition->round)
            ->where(function ($query) use ($results) {
                $query->where('team_id', $results['team_1'])
                    ->orWhere('team_id', $results['team_2']);
            })
            ->get();

        if ($scores->count() !== 2) {
            return back()->with('alert', __('competitions/management.results_teams_error'));
        }

        if ($scores[0]->touchdowns !== 0 || $scores[1]->touchdowns !== 0) {
            return back()->with('alert', __('competitions/management.results_repeat_error'));
        }

        // Check that the teams play against each other and it is the first time results are registered
        if (($scores[0]->order % 2 === 0 && $scores[1]->order === $scores[0]->order + 1) ||
            ($scores[1]->order % 2 === 0 && $scores[0]->order === $scores[1]->order + 1)
        ) {
            $scores = $scores->keyBy('team_id');
            foreach ($this->formatResults($results) as $key => $result) {
                $scores[$key]->touchdowns += $result['touchdowns'];
                $scores[$key]->touchdowns_diff += $result['touchdowns_diff'];
                $scores[$key]->score += $result['points'];
                $scores[$key]->save();
            }
            $this->createMatchLogAndHistory($results);
        } else {
            return back()->with('alert', __('competitions/management.results_wrong_teams'));
        }

        return back()->with('success', __('competitions/management.save_success'));
    }

    /**
     * Get current competition first round of play off
     *
     * @return mixed
     */
    protected abstract function getPlayOffStartRound();

    /**
     * Get current competition number of play off players.
     *
     * @return mixed
     */
    protected abstract function getPlayOffTeamsNumber();

    /**
     * Get the max round for the competition.
     *
     * @return int
     */
    public abstract function maxRound(): int;

    /**
     * Get the number of matches should be played for the current round in order
     * to check if the round is finished.
     *
     * @return bool
     */
    public abstract function checkRequiredCurrentRoundMatches(): bool;

    /**
     * Create trophies for a finished competition.
     */
    protected abstract function createTrophies();
}
