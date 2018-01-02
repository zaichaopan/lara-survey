<?php

namespace Tests\Feature;

use Tests\TestCase;
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

        $this->get(route('surveys.questions.edit', ['survey' => $survey, 'question' => $question]))->assertRedirect('login');

        // redirect because of unauthenticated
        $this->put(route('surveys.questions.update', ['survey' => $survey, 'question' => $question]), [])->assertStatus(302);

        $this->actingAs($jane);
        $this->get(route('surveys.questions.edit', ['survey' => $survey, 'question' => $question]))->assertStatus(403);

        $this->put(route('surveys.questions.update', ['survey' => $survey, 'question' => $question]), [])->assertStatus(403);
    }

    /** @test */
    public function author_can_update_multiple_choice_question()
    {
    }

    protected function updateQuestion($overrides = [])
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

        return $this->post(route('surveys.questions.store', [
            'survey' => $survey,
        ]), array_merge($question, $overrides));
    }
}
