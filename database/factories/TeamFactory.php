<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    $races = races();
    return [
        'user_id' => 1,
        'name' => uniqid('team_'),
        'race_id' => $races->get(rand(0, $races->count() - 1)),
    ];
});
