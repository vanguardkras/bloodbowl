<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Race;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupRoundsLogicTest extends TestCase
{
    use RefreshDatabase;

    public function testFullCircleTest()
    {
        $teams_number = 12;
        $competition = $this->createParametrisedCompetition(4, 4);
        $this->createFakeTeamsForCompetition($competition, $teams_number);
        $competition->nextRound();
        $this->assertEquals($competition->scores()->count(), $teams_number);

        $iteration = 1;
        foreach ($competition->scores()->orderBy('order')->get() as $score) {
            $score->score = $iteration + ($iteration % 2 ? 3 : 0);
            $score->round = 1;
            $score->save();
            $iteration++;
        }

        $first_score = $competition->scores()->where('order', 1)->first();
        $first_score->round = 0;
        $first_score->save();
        $competition->nextRound();
        $this->assertEquals(1, $competition->round);

        $first_score->round = 1;
        $first_score->save();
        $competition->nextRound();

        $this->assertEquals(2, $competition->round);
        //TODO: Continue logic check.
    }

    private function createParametrisedCompetition($groups_size, $group_rounds_play_off)
    {
        $user = $this->loginAsFakeUser();
        $competition = new Competition([
            'name' => 'TestCompetitionName',
            'type' => competitionTypes()[0],
            'tops_number' => 1,
            'self_confirm' => 1,
            'winner_points' => 3,
            'registration_end' => today()->toDateString(),
        ]);
        $competition->user_id = $user->id;
        $parameters = new \StdClass;
        $parameters->groups_size = $groups_size;
        $parameters->group_rounds_play_off = $group_rounds_play_off;
        $competition->parameters = $parameters;
        $competition->save();
        return $competition;
    }

    private function createFakeTeamsForCompetition(Competition $competition, $number)
    {
        $race = new Race;
        $race->name_en = 'test_race_en';
        $race->name_ru = 'test_race_ru';
        $race->save();

        for ($i = 0; $i < $number; $i++) {
            $team = new Team;
            $team->user_id = $competition->user_id;
            $team->name = uniqid('team_');
            $team->race_id = $race->id;
            $team->competition_id = $competition->id;
            $team->save();
        }
    }
}
