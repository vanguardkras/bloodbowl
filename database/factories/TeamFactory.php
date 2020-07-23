<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    $races = races();
    return [
        'user_id' => random_int(1, 2),
        'name' => uniqid('team_'),
        'race_id' => $races->get(rand(0, $races->count() - 1)),
    ];
});
