<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_author_cannot_add_questions_to_a_survey()
    {
        $john = factory('App\User')->create();
        $jane = factory('App\User')->create();
        $survey = factory('App\Survey')->create(['user_id' => $john->id]);
        $this->get(route('questions.create', ['survey' => 1]))->assertRedirect('login');
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
            'submittable_type' => 'invalid_type'
        ]))->assertStatus(404);
    }

    /** @test */
    public function question_submittable_type_is_required()
    {
        $this->createQuestion([
            'submittable_type' => null
        ])->assertSessionHasErrors('submittable_type');
    }

    /** @test */
    public function submittable_type_has_to_be_valid()
    {
        $this->createQuestion(['submittable_type' => 'invalid'])
            ->assertSessionHasErrors('submittable_type');
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
            'submittable_type' => 'multiple_choice_submittable',
            'options' => null,
        ])->assertSessionHasErrors('options');

        $this->createQuestion([
            'submittable_type' => 'multiple_choice_submittable',
            'options' => [],
        ])->assertSessionHasErrors('options');
    }

    /** @test */
    public function options_has_to_be_array_for_multiple_choice_submittable()
    {
        $this->createQuestion([
            'submittable_type' => 'multiple_choice_submittable',
            'options' => 'foo',
        ])->assertSessionHasErrors('options');
    }

    /** @test */
    public function minimum_scale_is_required_for_scale_submittable()
    {
        $this->createQuestion([
            'submittable_type' => 'scale_submittable',
            'minimum' => null,
        ])->assertSessionHasErrors('minimum');
    }

    /** @test */
    public function minimum_scale_has_to_be_an_integer_and_no_less_than_zero()
    {
        $this->createQuestion([
            'submittable_type' => 'scale_submittable',
            'minimum' => 'foo',
        ])->assertSessionHasErrors('minimum');

        $this->createQuestion([
            'submittable_type' => 'scale_submittable',
            'minimum' => -1,
        ])->assertSessionHasErrors('minimum');
    }

    /** @test */
    public function maximum_scale_is_required_for_scale_submittable()
    {
        $this->createQuestion([
            'submittable_type' => 'scale_submittable',
            'maximum' => null,
        ])->assertSessionHasErrors('maximum');
    }

    /** @test */
    public function maximum_has_to_an_integer_and_greater_than_minimum()
    {
        $this->createQuestion([
            'submittable_type' => 'scale_submittable',
            'maximum' => 'foo',
        ])->assertSessionHasErrors('maximum');

        $this->createQuestion([
            'submittable_type' => 'scale_submittable',
            'minimum' => 1,
            'maximum' => 1
        ])->assertSessionHasErrors('maximum');
    }

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


    protected function createQuestion($overrides = [])
    {
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $question = [
            'title' => 'foo',
            'submittable_type' => array_random([
                'multiple_choice_submittable',
                'open_submittable',
                'scale_submittable'])
        ];

        return $this->post(route('questions.store', [
            'survey' => $survey,
        ]), array_merge($question, $overrides));
    }
}
