<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Trophy
 * @package App\Models
 *
 * @property int competition_id
 * @property int team_id
 * @property int position
 */
class Trophy extends Model
{
    /**
     * Get current trophy competition data
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get current trophy team.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
