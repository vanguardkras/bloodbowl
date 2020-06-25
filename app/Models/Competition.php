<?php

namespace App\Models;

use App\User;
use App\Services\CompetitionStrategy\CompetitionStrategyException;
use App\Services\CompetitionStrategy\Type;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Competition
 *
 * @property int id
 * @property string name
 * @property string info
 * @property string logo
 * @property User user
 * @property string type
 * @property array races
 * @property array parameters
 * @property int self_confirm
 * @property int tops_number
 * @property int winner_points
 * @property int round
 * @property string registration_end
 * @property int max_teams
 * @property bool finished
 * @property Collection scores
 *
 * @package App\Models
 */
class Competition extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'parameters' => 'object',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'info',
        'registration_end',
        'self_confirm',
        'winner_points',
        'tops_number',
        'type',
    ];

    /**
     * Strategy instance
     *
     * @var Type
     */
    public $strategy;

    /**
     * Get the number of played matches for the current round
     *
     * @return int
     */
    public function getCurrentRoundPlayedMatches()
    {
        return $this->matchLogs()->where('round', $this->round)->count();
    }

    /**
     * Get the max round of the competition.
     *
     * @return int
     * @throws \ReflectionException
     */
    public function getMaxRound()
    {
        $this->checkStrategy();
        return $this->strategy->maxRound();
    }

    /**
     * Fill input parameters basing on the strategy.
     *
     * @throws \ReflectionException
     */
    public function fillParameters()
    {
        $this->checkStrategy();
        $this->strategy->fillParameters();
    }

    /**
     * Get current competition history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        return $this->hasMany(History::class);
    }

    /**
     * Get current competition logo
     *
     * @return string
     */
    public function logo()
    {
        $default_img = '/img/defaults/competition.jpg';
        return $this->logo ? '/storage/' . $this->logo : $default_img;
    }

    /**
     * Get current competition match logs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matchLogs()
    {
        return $this->hasMany(MatchLog::class);
    }

    /**
     * Start the next round
     *
     * @throws \ReflectionException
     */
    public function nextRound()
    {
        $this->checkStrategy();
        $this->strategy->nextRound();
    }

    /**
     * Get current competiton allowed races.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function races()
    {
        return $this->belongsToMany(Race::class);
    }

    /**
     * Add new match results to the current competition.
     *
     * @param array $results
     * @throws \ReflectionException
     */
    public function recordResults(array $results)
    {
        $this->checkStrategy();
        $this->strategy->recordResults($results);
    }

    /**
     * Get a collection of teams applied to current competition.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function registeredTeams()
    {
        return $this->belongsToMany(Team::class, 'registration_competition_team');
    }

    /**
     * Get current competition score data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    /**
     * Get current competition teams
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Current competition user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a list of competitions with the active registration stage.
     *
     * @return mixed
     */
    public static function getOpenRegistration()
    {
        return self::select(['id', 'name', 'logo', 'type', 'registration_end'])
            ->where('round', 0)
            ->orderBy('registration_end')->get();
    }

    /**
     * Check if the competition has a strategy.
     *
     * @throws \ReflectionException
     */
    public function checkStrategy()
    {
        if (!$this->strategy) {
            $this->setStrategy();
        }
    }

    /**
     * Determine a strategy for the current competition
     *
     * @throws \ReflectionException
     */
    public function setStrategy()
    {
        $strategyName = ucfirst(Str::camel($this->type));
        $reflection = new \ReflectionClass(Type::class);
        $strategyNamespace = $reflection->getNamespaceName();
        $reflection = new \ReflectionClass($strategyNamespace . '\\' . $strategyName);
        $this->strategy = $reflection->newInstance($this, request()->all());
    }
}
