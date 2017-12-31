<?php

use App\SubmitType;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use Faker\Generator as Faker;
use App\MultipleChoiceSubmittable;

$factory->define(App\Question::class, function (Faker $faker) {
    return [
        'survey_id' => function () {
            return factory('App\Survey')->create()->id;
        },
        'title' => $faker->sentence,
    ];
});

$factory->state(App\Question::class, 'multiple_choice', function ($faker) {
    return [
        'submittable_id' => MultipleChoiceSubmittable::create()->id,
        'submittable_type' => 'App\MultipleChoiceSubmittable'
    ];
});

$factory->state(App\Question::class, 'open', function ($faker) {
    return [
        'submittable_id' => OpenSubmittable::create()->id,
        'submittable_type' => 'App\OpenSubmittable'
    ];
});

$factory->state(App\Question::class, 'scale', function ($faker) {
    return [
        'submittable_id' => ScaleSubmittable::create()->id,
        'submittable_type' => 'App\ScaleSubmittable'
    ];
});
