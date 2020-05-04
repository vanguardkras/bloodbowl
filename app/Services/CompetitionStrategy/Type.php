<?php


namespace App\Services\CompetitionStrategy;

use App\Models\Competition;

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

    public function __construct(Competition $competition)
    {
        request()->validate($this->validationRules);
        $this->competition = $competition;
    }

    /**
     * Change competition instance parameters.
     */
    public abstract function fillParameters();
}
