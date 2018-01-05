<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewSummaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_view_survey_summary()
    {
        $this->get(route('surveys.summaries.show', ['survey' => 1]))->assertRedirect('login');
    }

    /** @test */
    public function auth_user_can_view_survey_summary()
    {
        $this->withoutExceptionHandling();
        $survey = factory('App\Survey')->create();

        $multipleChoiceQuestion = createMultipleChoiceQuestion($survey);

        $scaleSubmittable = factory('App\ScaleSubmittable')->create([
             'minimum' => 1,
             'maximum' => 5,
        ]);

        $scaleQuestion = factory('App\Question')->states('scale')->create([
             'survey_id' => $survey->id,
             'submittable_id' => $scaleSubmittable->id
         ]);

        $openQuestion = factory('App\Question')->states('open')->create(['survey_id' => $survey->id ]);

        $completion = factory('App\Completion')->create(['survey_id' => $survey->id ]);

        $answerMultipleChoice  = factory('App\Answer')->create([
             'question_id' => $multipleChoiceQuestion->id,
             'completion_id' => $completion->id,
             'text' => $multipleChoiceQuestion->options[0]->text
        ]);

        $answerScaleQuestion  = factory('App\Answer')->create([
             'question_id' => $scaleQuestion->id,
             'completion_id' => $completion->id,
             'text' => 4
        ]);

        $answerOpenQuestion  = factory('App\Answer')->create([
             'question_id' => $openQuestion->id,
             'completion_id' => $completion->id,
             'text' => 'Hello world!'
        ]);

        $this->login();

        $this->get(route('surveys.summaries.show', ['survey' => $survey]))
            ->assertSee($survey->title)
            ->assertSee("3 questions")
            ->assertSee("1 completions")
            ->assertSee($multipleChoiceQuestion->options[0]->text)
            ->assertSee('1 chosen')
            ->assertSee('100%')
            ->assertSee('Hello world');
    }
}
