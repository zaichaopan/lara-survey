<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function completions()
    {
        return $this->hasMany(Completion::class);
    }

    public function addSurvey($attributes)
    {
        return $this->surveys()->create($attributes);
    }

    public function hasCompleted(Survey $survey)
    {
        return $this->completions()->where('survey_id', $survey->id)->exists();
    }
}
