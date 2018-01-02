<?php

namespace Tests\Unit;

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
    public function it_has_questions()
    {
        $question = $this->addQuestion(['submittable_type' => 'open_submittable']);
        $this->assertTrue($this->survey->questions->contains($question));
    }

    public function it_can_be_completed()
    {
        $user = factory('App\User')->create();
        $completion = $this->survey->completedBy($user);
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
}
