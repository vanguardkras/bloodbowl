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
     * @param int $type_id
     * @return Competition
     */
    protected function createFakeCompetition(int $user_id, int $type_id = 2)
    {
        $competition = new Competition([
            'name' => 'TestCompetitionName',
            'type' => competitionTypes()[$type_id],
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
