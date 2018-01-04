<?php

use Faker\Generator as Faker;

$factory->define(App\ScaleSubmittable::class, function (Faker $faker) {
    return [
        'minimum' => 1,
        'maximum' => 5
    ];
});
