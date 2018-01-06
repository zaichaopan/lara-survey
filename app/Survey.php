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
        return collect($this->questions)->map->buildAnswer($attributes);
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
