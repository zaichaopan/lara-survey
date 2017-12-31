<?php

namespace Tests\Unit;

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
        $question =  $this->createQuestion('multiple_choice');
        $this->assertQuestionType($question, MultipleChoiceSubmittable::class);
    }

    /** @test */
    public function it_adds_open_questions()
    {
        $question =  $this->createQuestion('open');
        $this->assertQuestionType($question, OpenSubmittable::class);
    }

    /** @test */
    public function it_adds_scale_questions()
    {
        $question =  $this->createQuestion('scale');
        $this->assertQuestionType($question, ScaleSubmittable::class);
    }

    /** @test */
    public function it_has_questions()
    {
        $question = $this->createQuestion('open');
        $this->assertTrue($this->survey->questions->contains($question));
    }

    /** @test */
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

    protected function createQuestion($type)
    {
        $type = SubmitType::create($type);
        return $question = $this->survey->createQuestion('foo')->submitType($type);
    }
}
