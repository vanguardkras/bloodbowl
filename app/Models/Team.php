<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

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
     * Get a competition current team is applied to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function registeredCompetition()
    {
        return $this->belongsToMany(Competition::class, 'registration_competition_team');
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
