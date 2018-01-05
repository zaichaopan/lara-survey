<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    const SUBMITTABLE_TYPES = [
        'multiple_choice_submittable',
        'open_submittable',
        'scale_submittable'
    ];

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
        return  $this->submittable_type
            ? snake_case(class_basename($this->submittable_type))
            : null;
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

    public function buildAttributes($submittableType)
    {
        $class =  "App\\" . studly_case($submittableType);
        $this->submittable_type = $class;
        $this->submittable = new $class;
        optional_method($this->submittable)->buildAttributes($this);
        return $this;
    }

    public function switchType($attributes)
    {
        optional_method($this->submittable)->clean();
        $this->dissociateType();
        $newSubmittableType =  "App\\" . studly_case($attributes['submittable_type']);
        (new $newSubmittableType)->buildQuestion($this, $attributes);
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
        return $this->answers-> whereNotIn('text', [null, ''])->all();
    }
}
