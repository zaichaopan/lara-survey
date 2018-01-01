<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSurveysTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_create_survey_form()
    {
        $survey = factory('App\Survey')->create();
        $this->get(route('surveys.create'))
            ->assertSee('Please enter a title for your survey');
    }

    /** @test */
    public function user_can_create_survey()
    {
        $this->actingAs($user = factory('App\User')->create());
        $this->post(route('surveys.store'), ['title' => 'foo'])->assertStatus(200);
        $this->assertCount(1, $user->surveys);
    }
}
