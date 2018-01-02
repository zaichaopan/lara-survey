<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSurveysTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_view_create_survey_form()
    {
        $this->get(route('surveys.create'))
            ->assertRedirect('login');
    }

    /** @test */
    public function auth_users_can_view_create_survey_form()
    {
        $this->login();
        $this->get(route('surveys.create'))
            ->assertViewIs('surveys.create')
            ->assertSee('Title:');
    }

    /** @test */
    public function guest_users_cannot_create_survey()
    {
        $this->post(route('surveys.store'), ['title' => 'foo'])->assertRedirect('login');
    }

    /** @test */
    public function title_is_required_for_survey()
    {
        $this->login();
        $this->post(route('surveys.store'), ['title' => null])->assertSessionHasErrors('title');
    }

    /** @test */
    public function auth_users_can_create_survey()
    {
        $this->login();
        $this->post(route('surveys.store'), ['title' => 'foo']);
        $this->assertCount(1, auth()->user()->surveys);
        $this->assertEquals('foo', auth()->user()->surveys->first()->title);
    }

    protected function login($user = null)
    {
        $user = $user ?? factory('App\User')->create();
        $this->actingAs($user);
    }
}
