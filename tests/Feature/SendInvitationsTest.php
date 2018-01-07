<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Mail\Invitation as InvitationEmail;

class SendInvitationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->author = factory('App\User')->create();
        $this->survey = factory('App\Survey')->create(['user_id' => $this->author->id]);
        $this->other = factory('App\User')->create();
    }

    /** @test */
    public function guests_cannot_send_invitations()
    {
        $this->viewSendInvitationForm()->assertRedirect('login');
        $this->postInvitation()->assertStatus(302);
        $this->login($this->other);
        $this->viewSendInvitationForm()->assertStatus(403);
        $this->postInvitation()->assertStatus(403);
    }

    /** @test */
    public function recipient_email_is_required()
    {
        $this->login($this->author);
        $this->postInvitation([
            'email' => null
        ])->assertSessionHasErrors('email');
    }

    /** @test */
    public function recipient_email_has_to_be_valid()
    {
        $this->login($this->author);
        $this->postInvitation([
            'email' => 'invalid'
        ])->assertSessionHasErrors('email');
    }

    /** @test */
    public function message_is_required()
    {
        $this->login($this->author);
        $this->postInvitation([
            'message' => null
        ])->assertSessionHasErrors('message');
    }

    /** @test */
    public function author_can_send_invitation()
    {
        Mail::fake();
        $this->login($this->author);
        $recipientEmail = 'john@example.com';
        $this->postInvitation(['email' => $recipientEmail, 'message' => 'foobar']);
        Mail::assertSent(InvitationEmail::class, function ($mail) use ($recipientEmail) {
            return $mail->hasTo($recipientEmail);
        });

        $this->assertTrue($this->survey->hasSentTo($recipientEmail));
    }

    protected function viewSendInvitationForm()
    {
        return $this->get(route('surveys.invitations.create', ['survey' => $this->survey]));
    }

    protected function postInvitation($data = [])
    {
        return $this->post(route('surveys.invitations.store', ['survey' => $this->survey]), $data);
    }
}
