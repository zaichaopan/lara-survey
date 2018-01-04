<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $guarded = [];

    protected $with = ['author', 'questions'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function completions()
    {
        return $this->hasMany(Completion::class);
    }

    public function addQuestion(array $attributes)
    {
        $question  = $this->questions()->create(['title' => $attributes['title']]);
        $submittableType =  "App\\" . studly_case($attributes['submittable_type']);
        (new $submittableType)->buildQuestion($question, $attributes);
        return $question;
    }

    public function buildAnswers(array $attributes)
    {
        $questions = $this->questions;

        return collect($attributes)->map(function ($item) use ($questions) {
            $questionId = $item['question_id'];
            $text =  $item['text'];
            throw_exception_unless($question = $questions->firstWhere('id', $questionId));
            optional_method($question->submittable)->validAnswer($text);
            return new Answer(['question_id' => $questionId,'text' => $text]);
        });
    }

    public function completeBy(User $user, array $answersAttributes)
    {
        $completion =  $this->completions()->create(['user_id' => $user->id]);
        $completion->addAnswers($this->buildAnswers($answersAttributes));
        return $completion;
    }
}
