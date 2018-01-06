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
    public function answers_number_must_match_question_number()
    {
        $this->login();
        $survey = factory('App\Survey')->create();
        $questions = factory('App\Question', 2)->create(['survey_id' => $survey->id]);
        $this->postAnswers($survey, [
            'answers_attributes' => [
                "{$questions[0]->id}" =>[ 'text' => 'foobar' ],
             ]
        ])->assertSessionHasErrors('answers_attributes');
    }

    /** @test */
    public function can_not_answer_questions_in_other_surveys()
    {
        $this->login();
        $question = factory('App\Question')->create();
        $this->postAnswers($question->survey, [
            'answers_attributes' => [
                "100" => ['text' => 'foobar'],
             ]
        ])->assertSessionHas('message', 'Oops! Something went wrong!');
    }

    /** @test */
    public function multiple_choice_question_answer_must_exist_in_its_options()
    {
        $this->login();
        $question = createMultipleChoiceQuestion();
        $invalidAnswerText = 'foobar';
        $this->assertFalse(in_array($invalidAnswerText, $question->options->pluck('text')->all()));
        $this->postAnswers($question->survey, [
            'answers_attributes' => [
               "{$question->jd}" => ['text' => $invalidAnswerText]
             ]
        ])->assertSessionHas('message', 'Oops! Something went wrong!');
    }

    /** @test */
    public function scale_question_answer_must_between_min_and_max()
    {
        $this->login();
        $scaleSubmittable = factory('App\ScaleSubmittable')->create(['minimum' => 1, 'maximum' => 5]);
        $question = factory('App\Question')->states('scale')->create([
            'submittable_id' => $scaleSubmittable->id
        ]);
        $this->postAnswers($question->survey, [
            'answers_attributes' => [
                "{$question->id}" => ['text' => '11']
             ]
        ])->assertSessionHas('message', 'Oops! Something went wrong!');
    }

    /** @test */
    public function auth_user_can_complete_survey()
    {
        $this->login();
        $survey = factory('App\Survey')->create();
        $multipleChoiceQuestion = createMultipleChoiceQuestion($survey);
        $scaleQuestion = factory('App\Question')->states('scale')->create(['survey_id' => $survey->id]);
        $openQuestion = factory('App\Question')->states('open')->create(['survey_id' => $survey->id]);

        $this->postAnswers($survey, [
            'answers_attributes' => [
                "{$multipleChoiceQuestion->id}" =>[
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

        $survey = $survey->fresh();
        $this->assertTrue(auth()->user()->hasCompleted($survey));
        $this->assertCount(1, $survey->completions);
        $answers = $survey->completions[0]->answers;
        $this->assertCount(3, $answers);
        $this->assertEquals([
            $multipleChoiceQuestion->options->first()->text, $scaleQuestion->submittable->maximum, 'foobar'
        ], $answers->pluck('text')->all());
    }

    protected function postAnswers($survey, $data)
    {
        return $this->post(route('surveys.completions.store', ['survey' => $survey]), $data);
    }
}
