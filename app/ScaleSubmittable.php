<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function validAnswer($text)
    {
        throw_exception_unless($this->minimum <= $text && $text <= $this->maximum);

        return true;
    }
}
