<?php

namespace Tests\Unit;

use App\SubmitType;
use Tests\TestCase;

class SubmitTypeTest extends TestCase
{

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_throws_exception_for_invalid_submit_type()
    {
        SubmitType::create('invalid_type');
    }
}
