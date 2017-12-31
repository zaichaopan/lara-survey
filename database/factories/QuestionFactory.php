<?php

use Faker\Generator as Faker;

$factory->define(App\Question::class, function (Faker $faker) {
    return [
        'survey_id' => function () {
            return factory('App\Survey')->create()->id;
        },
        'title' => $faker->sentence,
    ];
});
