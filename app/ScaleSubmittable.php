<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InvalidAnswerException;

class ScaleSubmittable extends Model
{
    protected $guarded = [];

    public function buildQuestion(Question $question, array $questionAttributes)
    {
        $this->updateAttributes($questionAttributes);
        $question->associateType($this);
    }

    public function buildAttributes(Question $question)
    {
        $this->minimum = 0;
        $this->maximum = 1;
    }

    public function updateAttributes($questionAttributes)
    {
        $this->minimum =  $questionAttributes['minimum'];
        $this->maximum =  $questionAttributes['maximum'];
        return tap($this)->save();
    }

    public function validAnswerText($text)
    {
        throw_exception_unless($this->validScale($text), InvalidAnswerException::class);
        return true;
    }

    public function validScale($scale)
    {
        return $this->minimum <= $scale && $scale <= $this->maximum;
    }

    public function summary($question)
    {
        $options =collect(range($this->minimum, $this->maximum));
        return $question->optionSummary($options);
    }
}
