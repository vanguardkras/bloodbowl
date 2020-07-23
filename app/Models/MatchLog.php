<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MatchLog
 * @package App\Models
 *
 * @property int competition_id
 * @property int round
 * @property int team_id_1
 * @property int team_id_2
 * @property int score_1
 * @property int score_2
 * @property int history_id
 * @property int user_id
 * @property bool confirmed
 * @property string date
 */
class MatchLog extends Model
{
    /**
     * Current match log competition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get current match log history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function history()
    {
        return $this->belongsTo(History::class);
    }

    /**
     * Rejects the record
     *
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function reject()
    {
        $competition = $this->competition()->first('winner_points');

        $score_team_1 = Score::where([
            ['competition_id', $this->competition_id],
            ['round', $this->round],
            ['team_id', $this->team_id_1],
        ])->first();
        $score_team_2 = Score::where([
            ['competition_id', $this->competition_id],
            ['round', $this->round],
            ['team_id', $this->team_id_2],
        ])->first();

        /*$score_team_1->touchdowns -= $this->score_1;
        $score_team_2->touchdowns -= $this->score_2;
        $score_team_1->touchdowns_diff -= $this->score_1 - $this->score_2;
        $score_team_2->touchdowns_diff -= $this->score_2 - $this->score_1;
        $score_team_1->score -= $this->score_1 > $this->score_2 ? $competition->winner_points : 0;
        $score_team_2->score -= $this->score_2 > $this->score_1 ? $competition->winner_points : 0;
        $score_team_1->save();
        $score_team_2->save();*/

        $team_1 = Team::find($this->team_id_1);
        $team_2 = Team::find($this->team_id_2);
        $team_1->touchdowns = $team_1->touchdowns - $this->score_1;
        $team_2->touchdowns = $team_2->touchdowns - $this->score_2;
        $team_1->played = $team_1->played - 1;
        $team_2->played = $team_2->played - 1;

        if ($this->score_1 > $this->score_2) {
            $team_1->wins = $team_1->wins - 1;
        } elseif ($this->score_2 > $this->score_1) {
            $team_2->wins = $team_2->wins - 1;
        } elseif ($this->score_1 === $this->score_2) {
            $team_1->draws = $team_1->draws - 1;
            $team_2->draws = $team_2->draws - 1;
        }

        $team_1->save();
        $team_2->save();

        $history = $this->history;
        $this->delete();
        $history->delete();
    }

    /**
     * Get the first team information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teamLeft()
    {
        return $this->belongsTo(Team::class, 'team_id_1');
    }

    /**
     * Get the second team information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teamRight()
    {
        return $this->belongsTo(Team::class, 'team_id_2');
    }
}
