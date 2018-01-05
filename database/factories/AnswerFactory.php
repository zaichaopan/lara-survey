<?php

use Faker\Generator as Faker;

$factory->define(App\Answer::class, function (Faker $faker) {
    return [
        'question_id' => function () {
            return factory('App\Question')->create()->id;
        },
        'completion_id' => function () {
            return factory('App\Completion')->create()->id;
        },
        'text' => $faker->word
    ];
});
