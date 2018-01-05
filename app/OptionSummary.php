<?php

namespace App;

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
}
