<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class History
 * @package App\Models
 *
 * @property int competition_id
 * @property int team_id_1
 * @property int team_id_2
 * @property int race_team_1
 * @property int race_team_2
 * @property int score_1
 * @property int score_2
 * @property string date
 * @property string team_name_1
 * @property string team_name_2
 */
class History extends Model
{
    protected $with = [
        'race_1',
        'race_2'
    ];

    /**
     * Get current record competition data
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get 'win', 'draw', or 'loss' for highlighting purposes.
     *
     * @param $team_id
     * @return string
     */
    public function getSuccess($team_id)
    {
        if ($this->score_1 == $this->score_2) {
            return 'draw';
        }

        if ($this->team_id_1 == $team_id) {
            return $this->score_1 > $this->score_2 ? 'win' : 'loss';
        } elseif ($this->team_id_2 == $team_id) {
            return $this->score_2 > $this->score_1 ? 'win' : 'loss';
        }

        return 'loss';
    }

    /**
     * Get first race from the history
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function race_1()
    {
        return $this->belongsTo(Race::class, 'race_team_1');
    }

    /**
     * Get the second race from the history
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function race_2()
    {
        return $this->belongsTo(Race::class, 'race_team_2');
    }

    /**
     * Get team_1 data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team_1()
    {
        return $this->belongsTo(Team::class, 'team_id_1');
    }

    /**
     * Get team_2 data
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team_2()
    {
        return $this->belongsTo(Team::class, 'team_id_2');
    }
}
