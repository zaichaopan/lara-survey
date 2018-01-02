<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenSubmittable extends Model
{
    protected $guarded = [];

    public function buildQuestion(Question $question, array $questionAttributes)
    {
        $question->associateType(tap($this)->save());
    }
}
