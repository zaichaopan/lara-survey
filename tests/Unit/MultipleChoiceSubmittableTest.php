<?php

namespace Tests\Unit;

use App\Option;
use Tests\TestCase;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultipleChoiceSubmittableTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_valid_answer()
    {
        $question = factory('App\Question')->create();
        $submittableType = new MultipleChoiceSubmittable;
        $submittableType->save();
        $question->associateType($submittableType);
        $question->addOptions([new Option(['text' => 'foo'])]);
        $submittableType = $submittableType->fresh();
        $this->assertTrue($submittableType->validAnswer('foo'));
        $this->expectException(\Exception::class);
        $submittableType->validAnswer('foobar');
    }
}
