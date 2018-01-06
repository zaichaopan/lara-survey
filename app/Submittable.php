<?php

namespace App;

class Submittable extends StrategyFinder
{
    const ACCEPTABLE_TYPES = [
        'open_submittable' => 'App\OpenSubmittable',
        'scale_submittable' => 'App\ScaleSubmittable',
        'multiple_choice_submittable' => 'App\MultipleChoiceSubmittable',
        'default' => 'App\MultipleChoiceSubmittable'
    ];
}
