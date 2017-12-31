<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    //
    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function createQuestion($title)
    {
        return $this->questions()->create(['title' => $title]);
    }
}
