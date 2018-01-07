<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Completion extends Model
{
    protected $guarded = [];

    protected $with = ['answers', 'survey'];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function invitation()
    {
        return $this->belongsTo(invitation::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function addAnswers($answers)
    {
        return $this->answers()->saveMany($answers);
    }
}
