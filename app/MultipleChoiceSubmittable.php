<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InvalidAnswerException;

class MultipleChoiceSubmittable extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->morphOne('App\Question', 'submittable');
    }

    public function buildQuestion(Question $question, array $questionAttributes)
    {
        $options = collect($questionAttributes['options'])->map(function ($optionAttribute) {
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
            $option->update(['text' => $optionAttributes[$key]]);
        });
    }

    public function clean()
    {
        $this->question->deleteOptions();
    }

    public function validAnswer($text)
    {
        throw_exception_unless($this->question->findOptionByText($text), InvalidAnswerException::class);
        return true;
    }

    public function summary($question)
    {
        $options = $question->options->pluck('text');
        return $question->optionSummary($options);
    }
}
