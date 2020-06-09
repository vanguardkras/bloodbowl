<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Score
 * @package App\Models
 *
 * @property int competition_id
 * @property int team_id
 * @property int score
 * @property int touchdowns
 * @property int touchdowns_diff
 * @property int round
 * @property int order
 */
class Score extends Model
{
    /**
     * Get current competitions team
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
