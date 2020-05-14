<?php

namespace Tests\Feature;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
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
        $this->get('/profile')->assertOk();
        $this->followingRedirects()->post('/profile_data_update', [
            'name' => 'TestCoachName',
            '_token' => csrf_token(),
            '_method' => 'PATCH',
            ])->assertOk();
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
        $this->followingRedirects()->post('teams', [
            'name' => 'TestTeamUniqueName',
            'race_id' => 1,
        ])->assertOk()->assertSee('has been successfully created');
        $this->followingRedirects()->post('/teams/' . $team->id, [
            '_token' => csrf_token(),
            '_method' => 'DELETE',
        ])->assertOk()->assertSeeText('has been successfully deleted');
    }

    public function testCompetitionsPageTest()
    {
        $user = $this->loginAsFakeUser();
        $user->commissioner = true;
        $user->save();
        $competition = $this->createFakeCompetition($user->id);
        $this->get('/competitions')->assertOk();
        $this->get('/competitions/create')->assertOk();
        $this->get('/competitions/' . $competition->id)->assertOk();
        $this->get('/competitions/' . $competition->id . '/edit')->assertOk();
        $this->followingRedirects()->post('/competitions', [
            'name' => 'TestCompetitionNameUnique',
            'registration_end' => today()->addDays(3)->toDateString(),
            'self_confirm' => '0',
            'any_max_teams' => '1',
            'max_teams' => '2',
            'winner_points' => '3',
            'tops_number' => '1',
            'races' => [1,2,3,4,5,6,7,8,9,10],
            'groups_size' => '4',
            'group_rounds_play_off' => '4',
            'type' => competitionTypes()[0],
            '_token' => csrf_token(),
        ])->assertOk()->assertSeeText('has been successfully created');
        $this->followingRedirects()->post('/competitions/' . $competition->id, [
            'registration_end' => today()->addDays(3)->toDateString(),
            'request' => 'edit',
            'self_confirm' => '0',
            'any_max_teams' => '1',
            'max_teams' => '2',
            'winner_points' => '3',
            'tops_number' => '1',
            'races' => [1,2,3,4,5,6,7,8,9,10],
            'type' => competitionTypes()[0],
            '_token' => csrf_token(),
            '_method' => 'PATCH',
        ])->assertOk()->assertSeeText('has been successfully updated');
        $this->followingRedirects()->post('/competitions/' . $competition->id, [
            '_token' => csrf_token(),
            '_method' => 'DELETE',
        ])->assertOk()->assertSeeText('has been successfully deleted');
    }

    public function testPublicCompetitionPageTest()
    {
        $competition = $this->createFakeCompetition(1);
        $this->get('/competitions/'.$competition->id.'/show')->assertOk();
        $this->loginAsFakeUser();
        $this->get('/competitions/'.$competition->id.'/show')->assertOk();
    }

    public function testCoachPageTest()
    {
        $user = $this->loginAsFakeUser();
        $this->get('/user/' . $user->id)->assertOk();
        Auth::logout();
        $this->get('/user/' . $user->id)->assertOk();
    }
}
