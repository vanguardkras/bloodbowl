<?php

namespace App\Models;

use App\User;
use App\Services\CompetitionStrategy\CompetitionStrategyException;
use App\Services\CompetitionStrategy\Type;
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
    protected $strategy;

    /**
     * Fill input parameters basing on the strategy.
     *
     * @throws CompetitionStrategyException
     */
    public function fillParameters()
    {
        $this->checkStrategy();
        $this->strategy->fillParameters();
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
     * Get current competiton allowed races.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function races()
    {
        return $this->belongsToMany(Race::class);
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
        $this->strategy = $reflection->newInstance($this);
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
     * @throws CompetitionStrategyException
     */
    protected function checkStrategy()
    {
        if (!$this->strategy) {
            throw new CompetitionStrategyException();
        }
    }
}
