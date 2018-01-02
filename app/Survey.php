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

    // public function completions()
    // {
    //     return $this->hasMany(Completion::class);
    // }

    public function addQuestion(array $attributes)
    {
        $question  = $this->questions()->create(['title' => $attributes['title']]);
        $submittableType =  "App\\" . studly_case($attributes['submittable_type']);
        (new $submittableType)->buildQuestion($question, $attributes);
        return $question;
    }

    // public function completedBy(User $user)
    // {
    //     return $this->completions()->create([
    //         'user_id' => $user->id
    //     ]);
    // }
}
