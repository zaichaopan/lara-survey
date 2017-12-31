<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScaleSubmittable extends Model
{
    protected $guarded = [];

    public function updateScale($scale)
    {
        return tap($this)->update($scale);
    }
}
