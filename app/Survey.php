<?php

namespace App;

use App\Summaries\Breakdown;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Mail\Invitation as InvitationEmail;

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

    public function completeBy($invitation, array $answersAttributes)
    {
        $completion = $this->createCompleton($invitation);
        $completion->addAnswers($this->buildAnswers($answersAttributes));
        return $completion;
    }

    public function createCompleton(Invitation $invitation)
    {
        return $this->completions()->create(['invitation_id' => $invitation->id ]);
    }

    public function summary()
    {
        return new Breakdown($this);
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
            'token' => TokenGenerator::generate($data['email'])
           ]);
    }

    public function hasSentTo($recipient)
    {
        return $this->invitations()->where('recipient_email', $recipient)->exists();
    }

    public function findInvitationForToken($token)
    {
        return $this->invitations()->whereToken($token)->firstOrFail();
    }
}
