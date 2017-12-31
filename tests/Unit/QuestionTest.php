<?php

namespace Tests\Unit;

use App\SubmitType;
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

    /** @test */
    public function it_has_submittable_type_short_name()
    {
        $question = $this->createQuestion('multiple_choice');
        $this->assertEquals('multiple_choice_submittable', $question->submitType);

        $question = $this->createQuestion('open');
        $this->assertEquals('open_submittable', $question->submitType);

        $question = $this->createQuestion('scale');
        $this->assertEquals('scale_submittable', $question->submitType);
    }

    protected function createQuestion($type)
    {
        return $question = factory('App\Question')->create()->submitType(SubmitType::create($type));
    }
}
