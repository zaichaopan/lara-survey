<?php

namespace Tests\Unit;

use App\Option;
use Tests\TestCase;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\InvalidAnswerException;

class MultipleChoiceSubmittableTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_valid_answer_text()
    {
        $question = factory('App\Question')->create();
        $submittableType = new MultipleChoiceSubmittable;
        $submittableType->save();
        $question->associateType($submittableType);
        $question->addOptions([new Option(['text' => 'foo'])]);
        $submittableType = $submittableType->fresh();
        $this->assertTrue($submittableType->validAnswerText('foo'));
        $this->expectException(InvalidAnswerException::class);
        $submittableType->validAnswerText('foobar');
    }
}
