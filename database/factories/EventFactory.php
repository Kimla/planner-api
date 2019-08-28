<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\User::class),
        'title' => $faker->sentence(2),
        'description' => $faker->sentence(4),
        'date' => $faker->dateTimeBetween('-1 years', '+1 years')->format('Y-m-d'),
    ];
});
