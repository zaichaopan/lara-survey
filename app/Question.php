<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    protected $guarded = [];

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
        var_dump($this->submittable_type);

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
}
