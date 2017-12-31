<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_can_add_survey()
    {
        $user = factory('App\User')->create();
        $user->addSurvey([
            'title' => 'Foo'
        ]);
        $this->assertTrue($user->surveys->contains('author_id', $user->id));
    }
}
