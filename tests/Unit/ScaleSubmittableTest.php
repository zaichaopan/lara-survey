<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ScaleSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScaleSubmittableTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_valid_answer()
    {
        $question = factory('App\Question')->create();
        $scaleSubmittable = new ScaleSubmittable([
            'minimum' => 1,
            'maximum' => 10
        ]);
        $scaleSubmittable->save();
        $question->associateType($scaleSubmittable);
        $scaleSubmittable = $scaleSubmittable->fresh();
        $this->assertTrue($scaleSubmittable->validAnswer(1));
        $this->assertTrue($scaleSubmittable->validAnswer(10));
        $this->assertTrue($scaleSubmittable->validAnswer(5));
        $this->assertTrue($scaleSubmittable->validAnswer('1'));
        $this->assertTrue($scaleSubmittable->validAnswer('10'));
        $this->expectException(\Exception::class);
        $scaleSubmittable->validAnswer('11');
    }
}
