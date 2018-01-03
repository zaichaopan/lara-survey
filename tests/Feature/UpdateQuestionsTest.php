<?php

namespace Tests\Feature;

use App\Option;
use Tests\TestCase;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_author_cannot_see_edit_questions()
    {
        $john = factory('App\User')->create();
        $survey = factory('App\Survey')->create(['user_id' => $john]);
        $question = factory('App\Question')->create(['survey_id' => $survey->id]);
        $jane = factory('App\User')->create();

        $this->viewEditForm($question)->assertRedirect('login');

        // redirect because of unauthenticated
        $this->updateQuestion($question, [])->assertStatus(302);

        $this->login($jane);
        $this->viewEditForm($question)->assertStatus(403);
        $this->updateQuestion($question, [])->assertStatus(403);
    }

    /** @test */
    public function author_can_update_multiple_choice_question()
    {
        $question = $this->createQuestion(MultipleChoiceSubmittable::class);

        $question->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar']),
            new Option(['text' => 'baz']),
        ]);

        $this->updateQuestion($question, [
            'title' => 'new title',
            'submittable_type' => 'multiple_choice_submittable',
            'options' => ['new foo', 'new bar' , 'new baz']
        ])->assertRedirect(route('surveys.show', ['survey' => $question->survey_id]));

        $question = $question->fresh();
        $this->assertEquals('new title', $question->title);
        $this->assertEquals([
            'new foo', 'new bar', 'new baz'
        ], $question->options->pluck('text')->all());
    }

    /** @test */
    public function author_can_update_open_questions()
    {
        $question = $this->createQuestion(OpenSubmittable::class);
        $this->updateQuestion($question, [
            'title' => 'new title',
            'submittable_type' => 'open_submittable',
        ])->assertRedirect(route('surveys.show', ['survey' => $question->survey_id]));
        $this->assertEquals('new title', $question->fresh()->title);
    }

    /** @test */
    public function author_can_update_scale_questions()
    {
        $question = $this->createQuestion(ScaleSubmittable::class);
        $this->updateQuestion($question, [
            'title' => 'new title',
            'submittable_type' => 'scale_submittable',
            'minimum' => 1,
            'maximum' => 10
        ])->assertRedirect(route('surveys.show', ['survey' => $question->survey_id]));
        $question = $question->fresh();
        $this->assertEquals('new title', $question->title);
        $this->assertEquals(1, $question->submittable->minimum);
        $this->assertEquals(10, $question->submittable->maximum);
    }

    /** @test */
    public function author_can_change_question_type()
    {
        $this->withoutExceptionHandling();
        $question = $this->createQuestion(MultipleChoiceSubmittable::class);
        $question->addOptions($this->options());
        $this->get(route('questions.types.create', [
            'question' => $question,
            'submittable_type' => 'open_submittable'
        ]))->assertViewIs('types.create')->assertSee($question->title);

        $this->post(route('questions.types.store', [
            'question' => $question,
            'submittable_type' => 'open_submittable'
        ]))->assertStatus(200);

        //     $this->get(route('questions.types.create', [
    //         'question' => $question,
    //         'submittable_type' => 'scale_submittable'
    //     ]))->assertViewIs('types.create')
    //         ->assertSee('Minimum')
    //         ->assertSee('Maximum');
    }




    protected function options()
    {
        return [
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar']),
            new Option(['text' => 'baz']),
        ];
    }

    protected function viewEditForm($question)
    {
        return $this->get(route('surveys.questions.edit', [
            'survey' => $question->survey_id,
            'question' => $question]));
    }
    protected function updateQuestion($question, $data)
    {
        return $this->patch(route('surveys.questions.update', [
            'survey' => $question->survey_id, 'question'=> $question
        ]), $data);
    }

    protected function createQuestion($submittableClass)
    {
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $question = factory('App\Question')->create(['survey_id' => $survey->id]);
        $submittable = new $submittableClass;
        $submittable->save();
        $question = $question->associateType($submittable);
        return $question;
    }
}
