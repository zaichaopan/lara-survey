<?php

namespace Tests\Unit;

use App\SubmitType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScaleSubmittableTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_update_scale()
    {
        $type = SubmitType::create('scale');
        $question = factory('App\Question')->create()->submitType($type);
        $scaleSubmittable = $question->submittable;
        $this->assertEquals(0, $scaleSubmittable->minimum);
        $this->assertEquals(0, $scaleSubmittable->maximum);
        $scaleSubmittable = $question->submittable->updateScale([
            'minimum' => 1,
            'maximum' => 10
        ]);
        $this->assertEquals(1, $scaleSubmittable->minimum);
        $this->assertEquals(10, $scaleSubmittable->maximum);
    }
}
