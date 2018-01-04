<?php

function createMultipleChoiceQuestion($survey = null)
{
    $survey = $survey ?? factory('App\Survey')->create();
    $question = factory('App\Question')->states('multiple_choice')->create(['survey_id' => $survey->id]);
    $options = factory('App\Option', 3)->create(['question_id' => $question->id ]);
    return $question;
}
