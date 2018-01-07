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
    public function it_can_add_answers()
    {
        $completion = factory('App\Completion')->create();
        $answers = $completion->addAnswers($this->answers());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $completion->answers);
    }

    protected function answers()
    {
        return [
            new Answer(['question_id' => 1,'text' => 'foo']),
            new Answer(['question_id' => 2,'text' => 'bar'])
       ];
    }
}
