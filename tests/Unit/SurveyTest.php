<?php

namespace Tests\Unit;

use App\Answer;
use App\Option;
use App\Question;
use App\SubmitType;
use Tests\TestCase;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyTest extends TestCase
{
    use  RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\User')->create();
        $this->survey = $this->user->addSurvey(['title' => 'foo']);
    }

    /** @test */
    public function it_has_an_author()
    {
        $this->assertEquals($this->user->id, $this->survey->author->id);
    }

    /** @test */
    public function it_has_questions()
    {
        $question = $this->addQuestion(['submittable_type' => 'open_submittable']);
        $this->assertTrue($this->survey->questions->contains($question));
    }

    /** @test */
    public function it_adds_multiple_choice_questions()
    {
        $question = $this->addQuestion([
            'submittable_type' => 'multiple_choice_submittable',
            'options' => ['foo', 'bar', 'baz']
        ]);

        $this->assertQuestionType($question, MultipleChoiceSubmittable::class);
        $this->assertEquals(['foo', 'bar', 'baz'], $question->options->pluck('text')->all());
    }

    /** @test */
    public function it_adds_open_questions()
    {
        $question = $this->addQuestion(['submittable_type' => 'open_submittable']);
        $this->assertQuestionType($question, OpenSubmittable::class);
    }

    /** @test */
    public function it_adds_scale_questions()
    {
        $question = $this->addQuestion([
            'submittable_type' => 'scale_submittable',
            'minimum' => 1,
            'maximum' => 10
        ]);
        $this->assertQuestionType($question, ScaleSubmittable::class);
        $this->assertEquals(1, $question->submittable->minimum);
        $this->assertEquals(10, $question->submittable->maximum);
    }

    /** @test */
    public function it_throws_exception_if_a_unknown_question_found_during_building_answers()
    {
        $question = $this->createQuestion(
            MultipleChoiceSubmittable::class,
            ['survey_id' => $this->survey->id]
        );

        $attributes = [['question_id' => 100 ] ];
        $this->expectException(\Exception::class);
        $this->survey->buildAnswers($attributes);
    }

    /** @test */
    public function it_throws_exception_if_invalid_text_found_during_building_answer_for_multiple_choice_question()
    {
        $question = $this->createQuestion(
            MultipleChoiceSubmittable::class,
            ['survey_id' => $this->survey->id]
        );

        $question->addOptions([new Option(['text' => 'foo'])]);
        $attributes = [['question_id' => $question->id,'text' => 'foobar']];
        $this->expectException(\Exception::class);
        $this->survey->buildAnswers($attributes);
    }

    /** @test */
    public function it_throws_exception_if_invalid_text_found_during_building_answer_for_scale_question()
    {
        $question = $this->createQuestion(
            ScaleSubmittable::class,
            ['survey_id' => $this->survey->id]
        );
        $question->submittable->update(['minimum' => 1,'maximum' => 10]);
        $attributes = [['question_id' => $question->id,'text' => '11']];
        $this->expectException(\Exception::class);
        $this->survey->buildAnswers($attributes);
    }

    /** @test */
    public function it_can_build_answers()
    {
        $scaleQuestion = $this->createQuestion(
            ScaleSubmittable::class,
            ['survey_id' => $this->survey->id
        ]
        );

        $scaleQuestion->submittable->update(['minimum' => 1, 'maximum' => 10]);

        $multipleChoiceQuestion = $this->createQuestion(
            MultipleChoiceSubmittable::class,
            ['survey_id' => $this->survey->id]
        );

        $multipleChoiceQuestion->addOptions([new Option(['text' => 'foo'])]);

        $openQuestion = $this->createQuestion(
            OpenSubmittable::class,
            ['survey_id' => $this->survey->id]
        );

        $attributes = [
           ['question_id' => $scaleQuestion->id,'text' => 5],
           ['question_id' => $multipleChoiceQuestion->id,'text' => 'foo'],
           ['question_id' => $openQuestion->id,'text' => 'foobar'],
        ];

        $answers = $this->survey->fresh()->buildAnswers($attributes);
        $this->assertCount(3, $answers);
    }

    /** @test */
    public function it_can_be_completed()
    {
        $question = $this->createQuestion(
            MultipleChoiceSubmittable::class,
            ['survey_id' => $this->survey->id]
        );
        $question->addOptions([new Option(['text' => 'foo'])]);
        $user = factory('App\User')->create();
        $completion = $this->survey->completeBy($user, [
            [
                'question_id' => $question->id,
                'text' => 'foo'
            ]
        ]);
        $this->assertTrue($this->survey->completions->contains($completion->id));
    }

    protected function assertQuestionType($question, $klass)
    {
        $this->assertTrue($this->survey->questions->contains($question->id));
        $this->assertInstanceOf($klass, $question->submittable);
    }

    protected function addQuestion($overrides)
    {
        $data = [
            'title' =>  'foo',
            'submittable_type' => array_random(Question::SUBMITTABLE_TYPES)
        ];

        return $this->survey->addQuestion(array_merge($data, $overrides));
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
