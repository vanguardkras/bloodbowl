<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Team
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property string name
 * @property int race_id
 * @property string logo
 * @property int competition_id
 * @property int touchdowns
 * @property int played
 * @property int wins
 * @property int draws
 * @property int successful_games_percentage
 * @property Collection trophies
 * @property Race race
 * @property User user
 */
class Team extends Model
{
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->with = 'race:id,name_' . app()->getLocale() . ' as name';
    }

    /**
     * @return int
     */
    public function getTotalMatchesOpenLeagueAttribute()
    {
        return $this->matchLogLeft()->where('round', 1)->count() +
            $this->matchLogRight()->where('round', 1)->count();
    }

    /**
     * Get successfull games percentage
     *
     * @return int
     */
    public function getSuccessfulGamesPercentageAttribute()
    {
        if ($this->played == 0) {
            return 0;
        }
        return intval(round(100 * ($this->draws + $this->wins) / $this->played));
    }

    /**
     * Get current team full play history
     *
     * @return mixed
     */
    public function history()
    {
        $history = History::where('team_id_1', $this->id)
            ->orWhere('team_id_2', $this->id)
            ->orderBy('date', 'desc')->get();

        $history->map(function ($item) {
            $index = $this->id == $item->team_id_1 ? 2 : 1;
            $team_name = 'team_name_' . $index;
            $team = 'team_' . $index;
            $race = 'race_' . $index;
            $item->team_name = $item->$team_name;
            $item->team = $item->$team;
            $item->race_name = $item->$race->name();
        });

        return $history;
    }

    /**
     * Get current team logo.
     *
     * @return string
     */
    public function logo()
    {
        $default_img = '/img/defaults/team.jpg';
        return $this->logo ? '/storage/' . $this->logo : $default_img;
    }

    /**
     * Get a competition current team is applied to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function registeredCompetition()
    {
        return $this->belongsToMany(Competition::class, 'registration_competition_team');
    }

    /**
     * Match log where the team is team_id_1
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matchLogLeft()
    {
        return $this->hasMany(MatchLog::class, 'team_id_1');
    }

    /**
     * Match log where the team is team_id_2
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matchLogRight()
    {
        return $this->hasMany(MatchLog::class, 'team_id_2');
    }

    /**
     * Get current team trophies data
     *
     * @return mixed
     */
    public function trophies()
    {
        return $this->hasMany(Trophy::class);
    }

    /**
     * Get current team user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Current team race.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function race()
    {
        return $this->belongsTo(Race::class);
    }
}
