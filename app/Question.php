<?php

namespace App;

use App\Summaries\OptionSummary;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InvalidAnswerException;

class Question extends Model
{
    protected $guarded = [];

    protected $with = ['options', 'submittable', 'answers'];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function submittable()
    {
        return $this->morphTo();
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function submittableType()
    {
        return snake_case(class_basename($this->submittable_type));
    }

    public function associateType($type)
    {
        return tap($this, function ($question) use ($type) {
            $question->submittable()->associate($type)->save();
        });
    }

    public function addOptions($options)
    {
        return $this->options()->saveMany($options);
    }

    public function updateAttributes(array $attributes)
    {
        $this->update(['title' => $attributes['title']]);
        optional_method($this->submittable)->updateAttributes($attributes);
        return $this;
    }

    public function buildAttributes($type)
    {
        $class = Submittable::get($type);
        $this->submittable_type = $class;
        $this->submittable = new $class;
        optional_method($this->submittable)->buildAttributes($this);
        return $this;
    }

    public function switchType($attributes)
    {
        $class = Submittable::get($attributes['submittable_type']);
        optional_method($this->submittable)->clean();
        $this->dissociateType();
        (new $class)->buildQuestion($this, $attributes);
        return $this;
    }

    public function deleteOptions()
    {
        $this->options()->delete();
    }

    public function dissociateType()
    {
        return tap($this, function ($question) {
            $submittable = $question->submittable;
            $question->submittable()->dissociate($submittable)->save();
            $submittable->delete();
        });
    }

    public function findOptionByText($text)
    {
        return $this->options->firstWhere('text', $text);
    }

    public function summary()
    {
        return $this->submittable->summary($this);
    }

    public function optionSummary($options)
    {
        $answers = $this->answers;
        return collect($options)->map(function ($option) use ($answers) {
            return new OptionSummary($option, $answers);
        });
    }

    public function openSubmitSummary()
    {
        return $this->answers->whereNotIn('text', [null, ''])->all();
    }

    public function buildAnswer($attributes)
    {
        throw_exception_unless(
            isset($attributes[$this->id]),
            InvalidAnswerException::class
        );
        $text = $attributes[$this->id]['text'];
        optional_method($this->submittable)->validAnswerText($text);
        return new Answer(['question_id' => $this->id, 'text' => $text]);
    }

    public function alternativeTypes()
    {
        $currentType = (new \ReflectionObject($this->submittable))->getName();

        return
        array_keys(array_where(
            Submittable::AVAILABLE_TYPES,
            function ($value, $key) use ($currentType) {
                return $value != $currentType && $key != 'default';
            }
        ));
    }
}
