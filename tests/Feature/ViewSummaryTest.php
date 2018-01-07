<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->author = factory('App\User')->create();
        $this->survey = factory('App\Survey')->create([
            'user_id' => $this->author->id
        ]);
        $this->other = factory('App\User')->create();
    }

    /** @test */
    public function non_author_cannot_view_survey_summary()
    {
        $this->viewSummary()->assertRedirect('login');
        $this->login($this->other);
        $this->viewSummary()->assertStatus(403);
    }

    /** @test */
    public function author_user_can_view_survey_summary()
    {
        $multipleChoiceQuestion = createMultipleChoiceQuestion($this->survey);

        $scaleSubmittable = factory('App\ScaleSubmittable')->create([
             'minimum' => 1,
             'maximum' => 5,
        ]);

        $scaleQuestion = factory('App\Question')->states('scale')->create([
             'survey_id' => $this->survey->id,
             'submittable_id' => $scaleSubmittable->id
         ]);

        $openQuestion = factory('App\Question')->states('open')->create(['survey_id' => $this->survey->id]);

        $completion = factory('App\Completion')->create(['survey_id' => $this->survey->id]);

        $answerMultipleChoice = factory('App\Answer')->create([
             'question_id' => $multipleChoiceQuestion->id,
             'completion_id' => $completion->id,
             'text' => $multipleChoiceQuestion->options[0]->text
        ]);

        $answerScaleQuestion = factory('App\Answer')->create([
             'question_id' => $scaleQuestion->id,
             'completion_id' => $completion->id,
             'text' => 4
        ]);

        $answerOpenQuestion = factory('App\Answer')->create([
             'question_id' => $openQuestion->id,
             'completion_id' => $completion->id,
             'text' => 'Hello world!'
        ]);

        $this->login($this->author);

        $this->viewSummary()
             ->assertSee($this->survey->title)
            ->assertSee('3 questions')
            ->assertSee('1 completions')
            ->assertSee($multipleChoiceQuestion->options[0]->text)
            ->assertSee('1 chosen')
            ->assertSee('100%')
            ->assertSee('Hello world');
    }

    protected function viewSummary()
    {
        return $this->get(route('surveys.summaries.show', ['survey' => $this->survey]));
    }
}
