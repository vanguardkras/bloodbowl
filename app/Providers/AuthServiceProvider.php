<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\MatchLog;
use App\Models\Team;
use App\Policies\CompetitionPolicy;
use App\Policies\MatchLogPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Competition::class => CompetitionPolicy::class,
        MatchLog::class => MatchLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
