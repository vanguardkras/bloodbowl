<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Score;
use Faker\Generator as Faker;

$factory->define(Score::class, function (Faker $faker) {
    return [
        'competition_id' => 1,
        'team_id' => rand(1, 10),
        'score' => rand(0, 15),
        'touchdowns' => rand(0, 30),
        'touchdowns_diff' => rand(0, 10),
    ];
});
