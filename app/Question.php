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

    protected $with = ['options', 'submittable'];

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

    public function getSubmitTypeAttribute()
    {
        return  $this->submittable_type
            ? snake_case(class_basename($this->submittable_type))
            : null;
    }

    public function submitType($type)
    {
        return tap($this, function ($question) use ($type) {
            $question->submittable()->associate($type)->save();
        });
    }

    public function addOption($attributes)
    {
        return $this->options()->create($attributes);
    }

    public function buildAnswerAttributes()
    {
        return  [
            'question_id' => $this->id,
            'text' => null
        ];
    }
}
