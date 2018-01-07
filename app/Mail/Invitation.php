<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Invitation as InvitationModel;

class Invitation extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    /**
     * Email Invitation Constructor
     *
     * @param InvitationModel $invitation
     */
    public function __construct(InvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.surveys.invitation');
    }
}
