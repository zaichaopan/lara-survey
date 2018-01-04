<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MultipleChoiceSubmittable;
use App\Option;
use App\ScaleSubmittable;
use App\OpenSubmittable;

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

    /** @test */
    public function answers_number_must_match_question_number()
    {
        $this->login();
        $survey = factory('App\Survey')->create();
        $questions = factory('App\Question', 2)->create(['survey_id' => $survey->id]);
        $this->postAnswers($survey, [
            'answers_attributes' => [
                [
                    'question_id' => $questions[0]->id
                ],
             ]
        ])->assertSessionHasErrors('answers_attributes');
    }

    /** @test */
    public function can_not_answer_questions_in_other_surveys()
    {
        $this->login();
        $survey = factory('App\Survey')->create();
        factory('App\Question')->create(['survey_id' => $survey->id]);
        $this->postAnswers($survey, [
            'answers_attributes' => [
                ['question_id' => 100],
             ]
        ])->assertSessionHas('message');
    }

    /** @test */
    public function multiple_choice_question_answer_must_exist_in_its_options()
    {
        $this->login();
        $survey = factory('App\Survey')->create();
        $question = $this->createQuestion(MultipleChoiceSubmittable::class, [
            'survey_id' => $survey->id
        ]);

        $question->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar'])
        ]);

        $this->postAnswers($survey, [
            'answers_attributes' => [
                [
                    'question_id' => $question->id,
                    'text' => 'foobar',
                ]
             ]
        ])->assertSessionHas('message');
    }

    /** @test */
    public function scale_question_answer_must_between_min_and_max()
    {
        $this->login();
        $survey = factory('App\Survey')->create();
        $question = $this->createQuestion(ScaleSubmittable::class, [ 'survey_id' => $survey->id ]);
        $question->fresh()->submittable->update(['minimum' => 1, 'maximum' => 10]);
        $this->postAnswers($survey, [
            'answers_attributes' => [
                [
                    'question_id' => $question->id,
                    'text' => '11',
                ]
             ]
        ])->assertSessionHas('message');
    }

    /** @test */
    public function auth_user_can_complete_survey()
    {
        $this->login();
        $survey = factory('App\Survey')->create();

        $multipleChoiceQuestion = $this->createQuestion(MultipleChoiceSubmittable::class, ['survey_id' => $survey->id]);

        $multipleChoiceQuestion->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar'])
        ]);

        $scaleQuestion = $this->createQuestion(ScaleSubmittable::class, ['survey_id' => $survey->id]);
        $scaleQuestion->fresh()->submittable->update(['minimum' => 1, 'maximum' => 10]);

        $openQuestion = $this->createQuestion(OpenSubmittable::class, ['survey_id' => $survey->id]);

        $this->postAnswers($survey, [
            'answers_attributes' => [
                [
                    'question_id' => $multipleChoiceQuestion->id,
                    'text' => 'foo',
                ],
                [
                    'question_id' => $scaleQuestion->id,
                    'text' => '5',
                ],
                [
                    'question_id' => $openQuestion->id,
                    'text' => 'foobar',
                ],
             ]
        ]);

        $survey = $survey->fresh();
        $this->assertTrue(auth()->user()->hasCompleted($survey));
        $this->assertCount(1, $survey->completions);
        $answers = $survey->completions[0]->answers;
        $this->assertCount(3,$answers);
        $this->assertEquals([
            $multipleChoiceQuestion->id, $scaleQuestion->id, $openQuestion->id
        ], $answers->pluck('question_id')->all());
        $this->assertEquals([
            'foo', '5', 'foobar'
        ], $answers->pluck('text')->all());
    }

    protected function postAnswers($survey, $data)
    {
        return $this->post(route('surveys.completions.store', ['survey' => $survey]), $data);
    }

   protected function createQuestion($submittableClass, $data = [])
    {
        $question = factory('App\Question')->create($data);
        $submittable = new $submittableClass;
        $submittable->save();
        $question = $question->associateType($submittable);
        return $question;
    }
}
