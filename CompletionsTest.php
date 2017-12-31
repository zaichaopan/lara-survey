<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompletionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_complete_a_survey()
    {
        $this->withoutExceptionHandling();
        $survey = factory('App\Survey')->create();

        $multipleChoiceQueston = factory('App\Question')
            ->states('multiple_choice')
            ->create(['survey_id' => $survey->id ]);

        $option = $multipleChoiceQueston->addOption(['text' => 'foo']);

        $openQuestion= factory('App\Question')
            ->states('open')
            ->create(['survey_id' => $survey->id ]);

        $scaleQuestion = factory('App\Question')
            ->states('scale')
            ->create(['survey_id' => $survey->id]);

        $scaleQuestion->submittable->updateScale([
            'minimum' => 1,
            'maximum' => 10
        ]);

        $this->postJson(route('completions.store', ['survey' => $survey]), [
            'answers' => [
                [
                    'question_id' => $multipleChoiceQueston->id,
                    'text' => $option->text
                ],
                [
                    'question_id' => $openQuestion->id,
                    'text' => 'foobar'
                ],
                [
                    'question_id' => $scaleQuestion->id,
                    'text' => $scaleQuestion->submittable->minimum
                ]
            ]
        ])->assertStatus(200);
    }
}
