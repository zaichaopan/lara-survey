<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $guarded = [];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
