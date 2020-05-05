<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Team;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function testMainPageTest()
    {
        $response = $this->get('/');
        $response->assertOk();
    }

    public function testAuthPagesTest()
    {
        $response = $this->get('/login');
        $response->assertOk(200);
        $response = $this->get('/register');
        $response->assertOk(200);
    }

    public function testProfilePageTest()
    {
        $this->loginAsFakeUser();
        $response = $this->get('/profile');
        $response->assertOk();
    }

    public function testTeamsPageTest()
    {
        $user = $this->loginAsFakeUser();
        $team = new Team;
        $team->user_id = $user->id;
        $team->race_id = 1;
        $team->name = 'TestTeamName';
        $team->save();
        $this->get('/teams')->assertOk();
        $this->get('/teams/create')->assertOk();
        $this->get('/teams/' . $team->id)->assertOk();
    }

    public function testCompetitionsPageTest()
    {
        $user = $this->loginAsFakeUser();
        $user->commissioner = true;
        $user->save();
        $type = competitionTypes()[2];
        $competition = new Competition([
            'name' => 'TestCompetitionName',
            'user_id' => $user->id,
            'type' => $type,
            'tops_number' => 1,
            'self_confirm' => 1,
            'winner_points' => 3,
            'registration_end' => today()->toDateString(),
        ]);
        $competition->user_id = $user->id;
        $competition->save();
        $this->get('/competitions')->assertOk();
        $this->get('/competitions/create')->assertOk();
        $this->get('/competitions/' . $competition->id)->assertOk();
    }
}
