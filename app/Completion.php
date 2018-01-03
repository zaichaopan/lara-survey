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

    public function participant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function addAnswers(array $answersAttributes)
    {
        $answers = collect($answersAttributes)->map(function ($answerAttributes) {
            return  new Answer([
                'question_id' => $answerAttributes['question_id'],
                'text' => $answerAttributes['text']
            ]);
        });
        return $this->answers()->saveMany($answers);
    }
}
