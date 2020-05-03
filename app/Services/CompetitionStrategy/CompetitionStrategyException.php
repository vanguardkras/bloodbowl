<?php

namespace App\Services\CompetitionStrategy;

class CompetitionStrategyException extends \Exception
{
    protected $message = 'Set a strategy for the competition before accessing its functionality ($competition->setStrategy())';

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }
}
