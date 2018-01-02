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

    // public function buildAnswerAttributes()
    // {
    //     return  [
    //         'question_id' => $this->id,
    //         'text' => null
    //     ];
    // }

    public function updateAttributes(array $questionAttributes)
    {
        $this->update(['title' => $questionAttributes['title']]);

        if (method_exists($this->submittable, 'updateAttributes')) {
            $this->submittable->updateAttributes($questionAttributes);
        }

        return $this->fresh();
    }

    public function buildAttributes($submittableType)
    {
        abort_unless(in_array($submittableType, static::SUBMITTABLE_TYPES), 404);
        $class =  "App\\" . studly_case($submittableType);
        $submittable  = new $class;
        $this->submittable = $submittable;
        $this->submittable_type = $class;

        if (method_exists($submittable, 'buildAttributes')) {
            $submittable->buildAttributes($this);
        }

        return $this;
    }
}
