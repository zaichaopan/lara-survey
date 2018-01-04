<?php

use Faker\Generator as Faker;

$factory->define(App\Option::class, function (Faker $faker) {
    return [
        'question_id' => function () {
            return factory('App\Question')->create()->id;
        },
        'text' => $faker->sentence()
    ];
});
