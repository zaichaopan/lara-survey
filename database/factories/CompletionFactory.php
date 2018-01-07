<?php

use Faker\Generator as Faker;

$factory->define(App\Completion::class, function (Faker $faker) {
    return [
        'invitation_id' => function () {
            return factory('App\Invitation')->create()->id;
        },
        'survey_id' => function () {
            return factory('App\Survey')->create()->id;
        }
    ];
});
