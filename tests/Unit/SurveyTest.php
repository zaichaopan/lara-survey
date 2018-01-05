<?php

namespace Tests\Unit;

use App\Question;
use Tests\TestCase;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use App\MultipleChoiceSubmittable;
use App\Exceptions\InvalidAnswerException;
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
        $question = factory('App\Question')->create(['survey_id' => $this->survey->id]);
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
        $question = factory('App\Question')->create(['survey_id' => $this->survey->id]);
        $attributes = [['question_id' => 100 ] ];
        $this->expectException(InvalidAnswerException::class);
        $this->survey->buildAnswers($attributes);
    }

    /** @test */
    public function it_throws_exception_if_invalid_text_found_during_building_answer_for_multiple_choice_question()
    {
        $multipleChoiceQuestion = createMultipleChoiceQuestion($this->survey);
        $this->assertFalse(in_array('foobar', $multipleChoiceQuestion->options->pluck('text')->all()));
        $attributes = [['question_id' => $multipleChoiceQuestion->id,'text' => 'foobar']];
        $this->expectException(InvalidAnswerException::class);
        $this->survey->buildAnswers($attributes);
    }

    /** @test */
    public function it_throws_exception_if_invalid_text_found_during_building_answer_for_scale_question()
    {
        $scaleSumittable = factory('App\ScaleSubmittable')->create(['minimum' => 0, 'maximum' => 5 ]);
        $scaleQuestion = factory('App\Question')->states('scale')->create([
            'survey_id' => $this->survey->id,
            'submittable_id' => $scaleSumittable->id
        ]);
        $attributes = [['question_id' => $scaleQuestion->id,'text' => '11']];
        $this->expectException(InvalidAnswerException::class);
        $this->survey->buildAnswers($attributes);
    }

    /** @test */
    public function it_can_build_answers()
    {
        $scaleSubmittable = factory('App\ScaleSubmittable')->create(['minimum' => 0, 'maximum' => 5 ]);
        $scaleQuestion = factory('App\Question')->states('scale')->create([
            'survey_id' => $this->survey->id,
            'submittable_id' => $scaleSubmittable->id
        ]);
        $multipleChoiceQuestion = createMultipleChoiceQuestion($this->survey);
        $openQuestion = factory('App\Question')->states('open')->create(['survey_id' => $this->survey->id]);

        $attributes = [
           ['question_id' => $scaleQuestion->id,'text' => 5],
           ['question_id' => $multipleChoiceQuestion->id,'text' => $multipleChoiceQuestion->options->first()->text],
           ['question_id' => $openQuestion->id,'text' => 'foobar']
        ];
        $answers = $this->survey->fresh()->buildAnswers($attributes);

        $this->assertCount(3, $answers);
    }

    /** @test */
    public function it_can_be_completed()
    {
        $user = factory('App\User')->create();
        $question = createMultipleChoiceQuestion($this->survey);
        $completion = $this->survey->completeBy($user, [
            [
                'question_id' => $question->id,
                'text' => $question->options->first()->text
            ]
        ]);
        $this->assertTrue($this->survey->completions->contains($completion->id));
    }

    /** @test */
    public function it_has_a_summary()
    {
        $summary = $this->survey->summary('break_down');

        $this->assertEquals(0, $summary->questionsCount);
        $this->assertEquals(0, $summary->completionsCount);
        $completion = factory('App\Completion')->create(['survey_id' => $this->survey->id]);
        factory('App\Question', 2)->create(['survey_id' => $this->survey->id]);
        tap($this->survey->fresh(), function ($survey) {
            $summary = $survey->summary('break_down');
            $this->assertEquals(2, $summary->questionsCount);
            $this->assertEquals(1, $summary->completionsCount);
        });
    }

    protected function assertQuestionType($question, $klass)
    {
        $this->assertTrue($this->survey->questions->contains($question->id));
        $this->assertInstanceOf($klass, $question->submittable);
    }

    protected function addQuestion($overrides)
    {
        $data = ['title' =>  'foo','submittable_type' => array_random(Question::SUBMITTABLE_TYPES)];
        return $this->survey->addQuestion(array_merge($data, $overrides));
    }
}
