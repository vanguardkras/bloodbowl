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
    public function playOffView() {
        //TODO: Add logic with receiving the whole table results to insert them
        $playOffRounds = $this->getPlayOffTeamsNumber();
        $lastRound = log10($playOffRounds) / log10(2);
        return view('competitions.types.common_play_off',
            compact(['playOffRounds', 'lastRound']));
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
        if ($this->round != $this->maxRound()) {
            return;
        }

        $this->competition->finished = today()->toDateString();
        $this->competition->save();

        $this->createTrophies();

        //MatchLog::where('competition_id', $this->competition->id)->delete();
        //Possibly, scores should be removed as well.
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
            for ($i = 0; $i < $number_of_players; $i++) {
                $score = new Score;
                $score->competition_id = $this->competition->id;
                $score->round = $this->competition->round + 1;
                $score->team_id = $current_scores[$i]->team_id;
                $score->order = $i >= ($number_of_players / 2) ?
                    (($number_of_players - $i - 1) * 2 + 1) :
                    2 * $i;
                $score->save();
            }
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
     */
    protected function createMatchLogAndHistory(array $results)
    {
        $team_1 = Team::find($results['team_1'], ['name', 'race_id']);
        $team_2 = Team::find($results['team_2'], ['name', 'race_id']);

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

        $mathLog = new MatchLog();
        $mathLog->competition_id = $this->competition->id;
        $mathLog->team_id_1 = $results['team_1'];
        $mathLog->team_id_2 = $results['team_2'];
        $mathLog->score_1 = $results['touchdowns_1'];
        $mathLog->score_2 = $results['touchdowns_2'];
        $mathLog->round = $this->competition->round;
        $mathLog->confirmed = $this->competition->self_confirm >= 2;
        $mathLog->date = today()->toDateString();
        $mathLog->history_id = $history->id;
        $mathLog->save();
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
    }

    /**
     * Create trophies for a finished competition.
     * @param bool $tops_number
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function createPlayOffTrophies($tops_number = false)
    {
        $scores = $this->competition->scores()
            ->orderBy('round', 'DESC')
            ->orderBy('score', 'DESC')
            ->orderBy('touchdowns_diff', 'DESC')
            ->orderBy('touchdowns', 'DESC')
            ->limit($tops_number ?: $this->competition->tops_number)
            ->get();

        $position = 1;
        foreach ($scores as $score) {
            $this->createTrophy($score->team_id, $position);
            $position++;
        }

        return $scores;
    }

    /**
     * Get current competition number of play off rounds.
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
    protected abstract function checkRequiredCurrentRoundMatches(): bool;

    /**
     * Create trophies for a finished competition.
     *
     * @param bool $tops_number
     * @return mixed
     */
    protected abstract function createTrophies($tops_number = false);
}
