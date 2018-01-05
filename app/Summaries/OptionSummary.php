<?php

namespace App\Summaries;

class OptionSummary
{
    protected $option;
    protected $answers;

    public function __construct($option, $answers)
    {
        $this->option = $option;
        $this->answers = $answers;
    }

    public function option()
    {
        return $this->option;
    }

    public function chosenCount()
    {
        return $this->answers->where('text', $this->option)->count();
    }

    public function answersCount()
    {
        return $this->answers->count();
    }

    public function chosenInPercentage()
    {
        return $this->answersCount()
            ? round($this->chosenCount()/$this->answersCount() * 100) . '%'
            :  '0%';
    }

    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return call_user_func([$this, $property]);
        }

        return "{$property} cannot be found!";
    }
}
