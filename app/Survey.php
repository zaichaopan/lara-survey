<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Mail\Invitation as InvitationEmail;
use Illuminate\Support\Facades\Mail;

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

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
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

    public function sendInvitation($data)
    {
        $invitation = $this->createInvitation($data);
        Mail::to($data['email'])->send(new InvitationEmail($invitation));
    }

    public function createInvitation($data)
    {
        return $this->invitations()->create([
            'recipient_email' => $data['email'],
            'message' => $data['message'],
            'token' => str_limit(md5($data['email'] . str_random()), 25, '')
           ]);
    }

    public function hasSentTo($recipient)
    {
       return $this->invitations()->where('recipient_email', $recipient)->exists();
    }
}
