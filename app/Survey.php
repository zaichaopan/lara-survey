<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
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

    public function createQuestion($attributes)
    {
        return $this->questions()->create($attributes);
    }

    public function completedBy(User $user)
    {
        return $this->completions()->create([
            'user_id' => $user->id
        ]);
    }
}
