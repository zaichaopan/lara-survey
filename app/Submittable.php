<?php

namespace App;


class Submittable
{
    const ACCEPTABLE_TYPES = [
        'open_submittable' => 'App\OpenSubmittable',
        'scale_submittable' => 'App\ScaleSubmittable',
        'multiple_choice_submittable' => 'App\MultipleChoiceSubmittable',
        'default' => 'App\MultipleChoiceSubmittable'
    ];

    public static function get($type)
    {
        return array_get(
            static::ACCEPTABLE_TYPES,
            $type,
            static::ACCEPTABLE_TYPES['default']
        );
    }

    public static function acceptTypes()
    {
        return array_keys(static::ACCEPTABLE_TYPES);
    }
}
