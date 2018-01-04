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

    /** @test */
    public function it_has_completions()
    {
        factory('App\Completion')->create(['user_id' => $this->user->id]);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->fresh()->completions);
    }

    /** @test */
    public function it_checks_if_a_user_has_completed_a_survey()
    {
        $survey = factory('App\Survey')->create();
        $this->assertFalse($this->user->hasCompleted($survey));
        factory('App\Completion')->create([
            'user_id' => $this->user->id,
            'survey_id' => $survey->id
        ]);
        $this->assertTrue($this->user->hasCompleted($survey));
    }
}
