<?php

namespace App\Summaries;

class UserAnswer
{
    protected $survey;

    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    public function type()
    {
        return 'user_answer';
    }

    public function survey()
    {
        return $this->survey;
    }

    public function answers()
    {
        return $this->survey->completionOf(auth()->user())->answers;
    }
}
