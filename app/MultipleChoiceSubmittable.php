<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MultipleChoiceSubmittable extends Model
{
    //
    protected $guarded = [];

    public function buildQuestion(Question $question, array $questionAttributes)
    {
        $options  = collect($questionAttributes['options'])->map(function ($optionAttribute) {
            return new Option(['text' => $optionAttribute]);
        });

        $this->save();
        $question->associateType($this)->addOptions($options);
    }
}
