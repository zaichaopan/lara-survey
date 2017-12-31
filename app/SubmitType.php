<?php

namespace App;

use Exception;

class SubmitType
{
    public static function create($type)
    {
        $validTypes = [
            'open' => new OpenSubmittable,
            'multiple_choice' => new MultipleChoiceSubmittable,
            'scale' => new ScaleSubmittable
       ];

        if (isset($validTypes[$type])) {
            return tap($validTypes[$type])->save();
        }

        throw new Exception('Not a valid question type');
    }
}
