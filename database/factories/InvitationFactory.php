<?php

use Faker\Generator as Faker;
use App\TokenGenerator;

$factory->define(App\Invitation::class, function (Faker $faker) {
    $recipientEmail = $faker->email;
    return [
        'survey_id' => function () {
            return factory('App\Survey')->create()->id;
        },
        'message' => $faker->paragraph,
        'recipient_email' => $recipientEmail,
        'token' => TokenGenerator::generate($recipientEmail)
    ];
});
