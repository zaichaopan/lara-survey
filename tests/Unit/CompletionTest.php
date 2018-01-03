<?php

namespace Tests\Unit;

use App\User;
use App\Answer;
use App\Survey;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompletionTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_survey()
    {
        $completion = factory('App\Completion')->create();
        $this->assertInstanceOf(Survey::class, $completion->survey);
    }

    /** @test */
    public function it_belongs_to_a_participant()
    {
        $completion = factory('App\Completion')->create();
        $this->assertInstanceOf(User::class, $completion->participant);
    }

    /** @test */
    public function it_can_add_answers()
    {
        $completion = factory('App\Completion')->create();
        $answers = $completion->addAnswers($this->answersAttributes());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $completion->answers);
        $this->assertEquals(
            $this->answersAttributes(),
            $completion->answers()->select('question_id', 'text')->get()->toArray()
        );
    }

    protected function answersAttributes()
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
