<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Score;
use Faker\Generator as Faker;

$factory->define(Score::class, function (Faker $faker) {
    return [
        'competition_id' => 1,
        'team_id' => rand(1, 8),
        'score' => 0,
        'touchdowns' => 0,
        'touchdowns_diff' => 0,
    ];
});
