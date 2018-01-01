<?php

use Faker\Generator as Faker;

$factory->define(App\Survey::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'user_id' => function () {
            return  factory('App\User')->create()->id;
        }
    ];
});
