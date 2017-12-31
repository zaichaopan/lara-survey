<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompletionTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_add_answer()
    {
        $question = factory('App\Question')->create();
        $completion = factory('App\Completion')->create([
            'survey_id' => $question->survey_id
       ]);

        $answer = $completion->addAnswer([
            'question_id' => $question->id,
            'text' => 'foo'
       ]);

        $this->assertTrue($completion->answers->contains($answer->id));
    }

    /** @test */
    public function it_can_add_answers()
    {
    }
}
