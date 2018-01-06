<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ScaleSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScaleSubmittableTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_valid_answer_text()
    {
        $question = factory('App\Question')->create();
        $scaleSubmittable = new ScaleSubmittable([
            'minimum' => 1,
            'maximum' => 10
        ]);
        $scaleSubmittable->save();
        $question->associateType($scaleSubmittable);
        $scaleSubmittable = $scaleSubmittable->fresh();
        $this->assertTrue($scaleSubmittable->validAnswerText(1));
        $this->assertTrue($scaleSubmittable->validAnswerText(10));
        $this->assertTrue($scaleSubmittable->validAnswerText(5));
        $this->assertTrue($scaleSubmittable->validAnswerText('1'));
        $this->assertTrue($scaleSubmittable->validAnswerText('10'));
        $this->expectException(\Exception::class);
        $scaleSubmittable->validAnswerText('11');
    }
}
