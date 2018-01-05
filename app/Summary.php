<?php

namespace App;

class Summary
{
    protected $survey;

    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    public function completionsCount()
    {
        return $this->survey->completions->count();
    }

    public function questionsCount()
    {
        return $this->survey->questions->count();
    }

    public function questions()
    {
        return $this->survey->questions;
    }

    public function survey()
    {
        return $this->survey;
    }

    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return call_user_func([$this, $property]);
        }

        return $this->survey->$property;
    }
}
