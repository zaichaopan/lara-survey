<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_adds_options()
    {
        $question = factory('App\Question')->create();
        $option = $question->addOption([
            'text' => 'foo',
            'score' => 1
        ]);
        $this->assertTrue($question->options->contains($option));
    }
}
