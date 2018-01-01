<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Completion extends Model
{
    protected $guarded = [];

    protected $with = ['answers', 'participant'];

    public function participant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function addAnswer($attributes)
    {
        return $this->answers()->create($attributes);
    }

    public function addAnswers($answerAttributeArray)
    {
        $answers = $this->buildAnswers($answerAttributeArray);
        return $this->answers()->saveMany($answers);
    }

    public function buildAnswers($answerAttributeArray)
    {
        return collect($answerAttributeArray)->map(function ($answerAttributes) {
            return new Answer(array_intersect_key(
                $answerAttributes,
                array_flip(['question_id', 'text'])
            ));
        });
    }
}
