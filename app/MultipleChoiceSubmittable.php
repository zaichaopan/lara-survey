<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MultipleChoiceSubmittable extends Model
{
    public function question()
    {
        return $this->morphOne('App\Question', 'submittable');
    }

    protected $guarded = [];

    public function buildQuestion(Question $question, array $questionAttributes)
    {
        $options  = collect($questionAttributes['options'])->map(function ($optionAttribute) {
            return new Option(['text' => $optionAttribute]);
        });

        $question->associateType(tap($this)->save())->addOptions($options);
    }

    public function buildAttributes(Question $question)
    {
        $question->options = collect(range(1, 3))->map(function () {
            return new Option;
        });

        return $question;
    }

    public function updateAttributes(array $questionAttributes)
    {
        $optionAttributes = $questionAttributes['options'];

        collect($this->question->options)->each(function ($option, $key) use ($optionAttributes) {
            $option->update([
                'text' => $optionAttributes[$key]
            ]);
        });
    }
}
