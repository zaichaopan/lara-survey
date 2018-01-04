<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompleteSurveysTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_user_cannot_complete_survey()
    {
        $this->get(route('surveys.show', ['survey' => 1]))->assertRedirect('login');
        $this->post(route('surveys.completions.store', ['survey' => 1]))->assertStatus(302);
    }

    /** @test */
    public function user_can_only_complete_once_for_a_survey()
    {
        $this->login();
        $completion = factory('App\Completion')->create(['user_id' => auth()->id()]);
        $this->postAnswers($completion->survey, [])->assertStatus(400);
    }

    /** @test */
    public function answers_attributes_are_required()
    {
        $this->login();
        $completion = factory('App\Completion')->create();
        $this->postAnswers($completion->survey, [])->assertSessionHasErrors('answers_attributes');
        $this->postAnswers($completion->survey, [
            'answers_attributes' => []
        ])->assertSessionHasErrors('answers_attributes');
    }

    /** @test */
    public function question_id_is_quired_in_answers_attributes()
    {
        $this->login();
        $completion = factory('App\Completion')->create();

        $response = $this->postAnswers($completion->survey, [
            'answers_attributes' => [
                [
                    'question_id' => null,
                ]
            ]
        ])->assertSessionHasErrors('answers_attributes.0.question_id');
    }

    protected function postAnswers($survey, $data)
    {
        return $this->post(route('surveys.completions.store', ['survey' => $survey]), $data);
    }
}
