<?php

namespace Tests\Unit;

use App\Answer;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompletionTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_build_answers()
    {
        $completion = factory('App\Completion')->create();
        $answers = $completion->buildAnswers($this->answerAttributeArray());
        $this->assertCount(2, $answers);
        $this->assertInstanceOf(Answer::class, $answers->first());
        $this->assertEquals(1, $answers->first()->question_id);
        $this->assertEquals('foo', $answers->first()->text);
    }

    /** @test */
    public function it_can_add_answers()
    {
        $completion = factory('App\Completion')->create();
        $completion->addAnswers($this->answerAttributeArray());
        $this->assertCount(2, $completion->fresh()->answers);
    }

    /** @test */
    public function it_can_build_answers_from_attributes()
    {
        $survey = factory('App\Survey')->create();

        $multipleChoiceQuestion = factory('App\Question')
            ->states('multiple_choice')
            ->create(['survey_id' => $survey->id ])
            ->addOption(['text' => 'foo']);

         $completion = factory('App\Completion')->create(['survey_id' => $survey->id]);
         $answers = $completion->buildAnswersFromQuestions();
         $this->assertCount(1, $answers);
         $this->assertEquals($multipleChoiceQuestion->id, $answers->first()->question->id);
         $this->assertEquals('multiple_choice_submittable', $answers->first()->question->submitType);
    }

    protected function answerAttributeArray()
    {
        return [
            [
                'question_id' => 1,
                'text' => 'foo'
            ],
            [
                'question_id' => 2,
                'text' => 'bar'
            ]
       ];
    }
}
