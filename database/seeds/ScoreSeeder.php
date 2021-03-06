<?php

use App\Models\Score;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = Team::all();
        $order = 0;
        foreach ($teams as $team) {
            factory(Score::class)->create(['team_id' => $team->id, 'order' => $order]);
            $order++;
        }
    }
}
