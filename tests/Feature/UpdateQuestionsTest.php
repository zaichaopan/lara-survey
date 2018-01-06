<?php

namespace Tests\Feature;

use App\Question;
use Tests\TestCase;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Submittable;

class UpdateQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_author_cannot_update_questions()
    {
        $john = factory('App\User')->create();
        $survey = factory('App\Survey')->create(['user_id' => $john]);
        $question = factory('App\Question')->create(['survey_id' => $survey->id]);
        $jane = factory('App\User')->create();

        $this->viewEditForm($question)->assertRedirect('login');

        $this->viewChangeTypeForm(
            $question,
            array_random(Submittable::available())
        )->assertRedirect('login');

        // redirect because of unauthenticated
        $this->updateQuestion($question, [])->assertStatus(302);
        $this->changeType($question, [])->assertStatus(302);

        $this->login($jane);
        $this->viewEditForm($question)->assertStatus(403);
        $this->updateQuestion($question, [])->assertStatus(403);
        $this->viewChangeTypeForm(
            $question,
            array_random(Submittable::available())
        )->assertStatus(403);
        $this->changeType($question, [])->assertStatus(403);
    }

    /** @test */
    public function author_can_update_multiple_choice_question()
    {
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $question = createMultipleChoiceQuestion($survey);
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
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $question = factory('App\Question')->states('open')->create(['survey_id' => $survey->id]);
        $this->updateQuestion($question, [
            'title' => 'new title',
            'submittable_type' => 'open_submittable',
        ])->assertRedirect(route('surveys.show', ['survey' => $question->survey_id]));
        $this->assertEquals('new title', $question->fresh()->title);
    }

    /** @test */
    public function author_can_update_scale_questions()
    {
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $scaleSubmittable = factory('App\ScaleSubmittable')->create(['minimum' => 1, 'maximum' => 5]);
        $question = factory('App\Question')->states('scale')->create([
            'submittable_id' => $scaleSubmittable->id,
            'survey_id' => $survey->id
        ]);
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
        $this->login();
        $survey = factory('App\Survey')->create(['user_id' => auth()->id()]);
        $question = createMultipleChoiceQuestion($survey);

        $this->viewChangeTypeForm($question, 'open_submittable');
        $this->changeType($question, ['submittable_type' => 'open_submittable']);
        $this->assertInstanceOf(OpenSubmittable::class, $question->fresh()->submittable);

        $this->viewChangeTypeForm($question, 'scale_submittable')
            ->assertSee('Minimum')
            ->assertSee('Maximum');

        $this->changeType($question, [
            'submittable_type' => 'scale_submittable',
            'minimum' => 1,
            'maximum' => 10
        ]);
        $question = $question->fresh();
        $this->assertInstanceOf(ScaleSubmittable::class, $question->submittable);
        $this->assertEquals(1, $question->submittable->minimum);
        $this->assertEquals(10, $question->submittable->maximum);

        $this->viewChangeTypeForm($question, 'multiple_choice_submittable')->assertSee('Option');
        $this->changeType($question, [
           'submittable_type' => 'multiple_choice_submittable',
            'options' => ['foo', 'bar', 'baz']
         ]);
        $question = $question->fresh();
        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $question->submittable);
        $this->assertCount(3, $question->options);
        $this->assertEquals(['foo', 'bar', 'baz'], $question->options->pluck('text')->all());
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

    protected function viewChangeTypeForm($question, $submittableType)
    {
        return $this->get(route('questions.types.create', [
            'question' => $question,
            'submittable_type' => $submittableType
        ]));
    }

    protected function changeType($question, $data)
    {
        return $this->post(route('questions.types.store', ['question' => $question]), $data);
    }
}
