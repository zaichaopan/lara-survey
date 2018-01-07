<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCompletionsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->survey = factory('App\Survey')->create();
        $this->invitation = factory('App\Invitation')->create([
            'survey_id' => $this->survey->id]);
        $this->completion = factory('App\Completion')->create([
            'invitation_id' => $this->invitation->id,
            'survey_id' => $this->survey->id
        ]);
    }

    /** @test */
    public function a_valid_token_is_required()
    {
        $this->view_copmletion('invalidToken')->assertStatus(404);
    }

    /** @test */
    public function recipient_can_view_his_completion()
    {
        $this->view_copmletion($this->invitation->token)
            ->assertViewIs('completions.show');
    }

    protected function view_copmletion($token)
    {
        return $this->get(route('surveys.completions.show', [
            'survey' => $this->survey,
            'completion' => $this->completion,
            'token' => $token
        ]));
    }
}
