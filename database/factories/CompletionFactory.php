<?php

use Faker\Generator as Faker;

$factory->define(App\Completion::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        'survey_id' => function () {
            return factory('App\Survey')->create()->id;
        }
    ];
});
