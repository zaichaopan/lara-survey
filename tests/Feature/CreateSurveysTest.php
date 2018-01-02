<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\MultipleChoiceSubmittable;
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

    /** @test */
    public function non_author_can_not_add_questions_to_a_survey()
    {
        $this->get(route('questions.create', ['survey' => 1]))->assertRedirect('login');
        $john = factory('App\User')->create();
        $jane = factory('App\User')->create();
        $survey = factory('App\Survey')->create(['user_id' => $john->id]);
        $this->login($jane);
        $this->get(route('questions.create', ['survey' => $survey]))->assertStatus(403);
    }

    /** @test */
    public function it_redirects_to_404_if_question_submittable_type_not_found()
    {
        $jane = factory('App\User')->create();
        $survey = factory('App\Survey')->create(['user_id' => $jane->id]);
        $this->login($jane);
        $this->get(route('questions.create', [
            'survey' => $survey,
            'question_submittable_type' => 'invalid_type'
        ]))->assertStatus(404);
    }

    /** @test */
    public function question_submittable_type_is_required_for_question()
    {
        $this->createQuestion([
            'question_submittable_type' => null
        ])->assertSessionHasErrors('question_submittable_type');
    }

    /** @test */
    public function question_submittable_type_has_to_be_valid()
    {
        $this->createQuestion(['question_submittable_type' => 'invalid'])
            ->assertSessionHasErrors('question_submittable_type');
    }

    /** @test */
    public function title_is_required()
    {
        $this->createQuestion(['title' => null])->assertSessionHasErrors('title');
    }

    /** @test */
    public function options_are_required_for_multiple_choice_submittable()
    {
        $this->createQuestion([
            'question_submittable_type' => 'multiple_choice_submittable',
            'options' => null,
        ])->assertSessionHasErrors('options');

        $this->createQuestion([
            'question_submittable_type' => 'multiple_choice_submittable',
            'options' => [],
        ])->assertSessionHasErrors('options');
    }

    /** @test */
    public function options_has_to_be_array_for_multiple_choice_submittable()
    {
        $this->createQuestion([
            'question_submittable_type' => 'multiple_choice_submittable',
            'options' => 'foo',
        ])->assertSessionHasErrors('options');
    }

    /** @test */
    public function minimum_scale_is_required_for_scale_submittable()
    {
        $this->createQuestion([
            'question_submittable_type' => 'scale_submittable',
            'minimum' => null,
        ])->assertSessionHasErrors('minimum');
    }

    /** @test */
    public function minimum_scale_has_to_be_an_integer_and_no_less_than_zero()
    {
        $this->createQuestion([
            'question_submittable_type' => 'scale_submittable',
            'minimum' => 'foo',
        ])->assertSessionHasErrors('minimum');

        $this->createQuestion([
            'question_submittable_type' => 'scale_submittable',
            'minimum' => -1,
        ])->assertSessionHasErrors('minimum');
    }

    /** @test */
    public function maximum_scale_is_required_for_scale_submittable()
    {
        $this->createQuestion([
            'question_submittable_type' => 'scale_submittable',
            'maximum' => null,
        ])->assertSessionHasErrors('maximum');
    }

    /** @test */
    public function maximum_has_to_an_integer_and_greater_than_minimum()
    {
        $this->createQuestion([
            'question_submittable_type' => 'scale_submittable',
            'maximum' => 'foo',
        ])->assertSessionHasErrors('maximum');

        $this->createQuestion([
            'question_submittable_type' => 'scale_submittable',
            'minimum' => 1,
            'maximum' => 1
        ])->assertSessionHasErrors('maximum');
    }

    /** @test */
    public function author_can_add_multiple_choice_questions()
    {
        $this->createQuestion([
            'question_submittable_type' => 'multiple_choice_submittable',
            'options' => ['foo', 'bar', 'baz'] ]);

        $survey = auth()->user()->surveys->first();
        $question = $survey->fresh()->questions->first();

        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $question->submittable);
        $this->assertEquals(['foo', 'bar', 'baz'], $question->options->pluck('text')->all());
    }


    public function author_can_update_mulitple_choice_questions()
    {
    }

    protected function createQuestion($overrides = [])
    {
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $question = [
            'title' => 'foo',
            'question_submittable_type' => array_random([
                'multiple_choice_submittable',
                'open_submittable',
                'scale_submittable'])
        ];

        return $this->post(route('questions.store', [
            'survey' => $survey,
        ]), array_merge($question, $overrides));
    }

    protected function login($user = null)
    {
        $user = $user ?? factory('App\User')->create();
        $this->actingAs($user);
    }
}
