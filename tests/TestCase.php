<?php

namespace Tests;

use App\Models\Competition;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @return User
     */
    protected function loginAsFakeUser()
    {
        $user = User::create(['username' => 'TestUserForTests']);
        $this->actingAs($user);
        return $user;
    }

    /**
     * Create a test competition.
     *
     * @param int $user_id
     * @return Competition
     */
    protected function createFakeCompetition(int $user_id)
    {
        $competition = new Competition([
            'name' => 'TestCompetitionName',
            'user_id' => $user_id,
            'type' => competitionTypes()[2],
            'tops_number' => 1,
            'self_confirm' => 1,
            'winner_points' => 3,
            'registration_end' => today()->toDateString(),
        ]);
        $competition->user_id = $user_id;
        $competition->save();
        return $competition;
    }
}
