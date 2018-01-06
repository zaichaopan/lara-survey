<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InvalidAnswerException;

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
        $question = $this->questions()->create(['title' => $attributes['title']]);
        $class = Submittable::get($attributes['submittable_type']);
        (new $class)->buildQuestion($question, $attributes);
        return $question;
    }

    public function buildAnswers(array $attributes)
    {
        $questions = $this->questions;
        return collect($attributes)->map(function ($item) use ($questions) {
            $text =  isset($item['text']) ? $item['text'] : '';
            throw_exception_unless(
                $question = $questions->firstWhere('id', $item['question_id']),
                InvalidAnswerException::class
            );
            optional_method($question->submittable)->validAnswer($text);
            return new Answer(['question_id' => $item['question_id'],'text' => $text]);
        });
    }

    public function completeBy(User $user, array $answersAttributes)
    {
        $completion = $this->completions()->create(['user_id' => $user->id]);
        $completion->addAnswers($this->buildAnswers($answersAttributes));
        return $completion;
    }

    public function summary($strategy)
    {
        $class = Summary::get($strategy);
        return new $class($this);
    }
}
