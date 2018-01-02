<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditQuestionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_author_cannot_see_edit_question_form()
    {
        $this->get(route('questions.edit', ['survey' => 1, 'question' => 1]))->assertRedirect('login');
        $this->actingAs($user = factory('App\User')->create());
        $question = factory('App\Question')->create();
        $this->get(route('questions.edit', ['survey' => $question->survey_id, 'question' => $question]))->assertStatus(403);
    }
}
