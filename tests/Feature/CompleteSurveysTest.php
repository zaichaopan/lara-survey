<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompleteSurveysTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->auth = factory('App\User')->create();
        $this->other = factory('App\User')->create();
        $this->survey = factory('App\Survey')->create();
        $this->recipientEmail = 'john@example.com';
        $this->invitation = factory('App\Invitation')->create(
            [
                'survey_id' => $this->survey->id,
                'recipient_email' => $this->recipientEmail
            ]
        );
    }

    /** @test */
    public function a_valid_token_is_required()
    {
        $this->viewSurvey('invalid_token')->assertStatus(404);
        $this->postAnswers(['token' => 'invalid_token'])->assertStatus(404);
    }

    /** @test */
    public function recipent_can_only_complete_once_for_a_survey()
    {
        $this->withoutExceptionHandling();
        $completion = factory('App\Completion')->create([
            'invitation_id' => $this->invitation->id,
            'survey_id' => $this->survey->id
        ]);
        $this->postAnswers(['token' => $this->invitation->token])
            ->assertSessionHas('message', 'You have already completed the survey!');
    }

    /** @test */
    public function answers_attributes_are_required()
    {
        $this->postAnswers(['token' => $this->invitation->token])
            ->assertSessionHasErrors('answers_attributes');

        $this->postAnswers([
            'token' => $this->invitation->token,
            'answers_attributes' => []])
            ->assertSessionHasErrors('answers_attributes');
    }

    /** @test */
    public function answers_number_must_match_question_number()
    {
        $questions = factory('App\Question', 2)->create(['survey_id' => $this->survey->id]);
        $this->postAnswers([
            'token' => $this->invitation->token,
            'answers_attributes' => [
                "{$questions[0]->id}" =>[ 'text' => 'foobar' ],
             ]
        ])->assertSessionHasErrors('answers_attributes');
    }

    /** @test */
    public function can_not_answer_questions_in_other_surveys()
    {
        $question = factory('App\Question')->create(['survey_id' => $this->survey->id]);
        $this->postAnswers([
            'token' => $this->invitation->token,
            'answers_attributes' => [
                "100" => ['text' => 'foobar'],
             ]
        ])->assertSessionHas('message', 'Oops! Something went wrong!');
    }

    /** @test */
    public function multiple_choice_question_answer_must_exist_in_its_options()
    {
        $question = createMultipleChoiceQuestion($this->survey);
        $invalidAnswerText = 'foobar';
        $this->assertFalse(in_array($invalidAnswerText, $question->options->pluck('text')->all()));
        $this->postAnswers([
            'token' => $this->invitation->token,
            'answers_attributes' => [
               "{$question->jd}" => ['text' => $invalidAnswerText]
             ]
        ])->assertSessionHas('message', 'Oops! Something went wrong!');
    }

    /** @test */
    public function scale_question_answer_must_between_min_and_max()
    {
        $scaleSubmittable = factory('App\ScaleSubmittable')->create(['minimum' => 1, 'maximum' => 5]);

        $question = factory('App\Question')->states('scale')->create([
            'submittable_id' => $scaleSubmittable->id,
            'survey_id' => $this->survey->id
        ]);

        $this->postAnswers([
            'token' => $this->invitation->token,
            'answers_attributes' => [
                "{$question->id}" => ['text' => '11']
             ]
        ])->assertSessionHas('message', 'Oops! Something went wrong!');
    }

    /** @test */
    public function recipient_can_complete_survey()
    {
        $multipleChoiceQuestion = createMultipleChoiceQuestion($this->survey);
        $scaleQuestion = factory('App\Question')->states('scale')
            ->create(['survey_id' => $this->survey->id]);
        $openQuestion = factory('App\Question')->states('open')
            ->create(['survey_id' => $this->survey->id]);

        $this->postAnswers([
            'token' => $this->invitation->token,
            'answers_attributes' => [
                "{$multipleChoiceQuestion->id}" => [
                    'text' => $multipleChoiceQuestion->options->first()->text,
                ],
                "{$scaleQuestion->id}" => [
                    'text' => $scaleQuestion->submittable->maximum,
                ],
                "{$openQuestion->id}" => [
                    'text' => 'foobar',
                ],
             ]
        ]);

        $this->survey = $this->survey->fresh();
        $this->assertCount(1, $this->survey->completions);
        $answers = $this->survey->completions[0]->answers;
        $this->assertCount(3, $answers);
        $this->assertEquals([
            $multipleChoiceQuestion->options->first()->text, $scaleQuestion->submittable->maximum, 'foobar'
        ], $answers->pluck('text')->all());
    }

    protected function viewSurvey($token)
    {
        return $this->get(route('surveys.completions.create', ['survey' => $this->survey, 'token' => $token]));
    }

    protected function postAnswers($data)
    {
        return $this->post(route('surveys.completions.store', ['survey' => $this->survey]), $data);
    }
}
