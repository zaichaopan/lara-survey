<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use  RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\User')->create();
    }

    /** @test */
    public function it_can_add_survey()
    {
        $this->user->addSurvey(['title' => 'Foo']);
        $this->assertTrue($this->user->surveys->contains('user_id', $this->user->id));
    }
}
