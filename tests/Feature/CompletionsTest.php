<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompletionsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->survey = factory('App\Survey')->create();

        $this->multipleChoiceQuestion = factory('App\Question')
            ->states('multiple_choice')
            ->create(['survey_id' => $this->survey->id ])
            ->addOption(['text' => 'foo']);

        $this->openQuestion= factory('App\Question')
            ->states('open')
            ->create(['survey_id' => $this->survey->id ]);

        $this->scaleQuestion = factory('App\Question')
            ->states('scale')
            ->create(['survey_id' => $this->survey->id]);
    }

    /** @test */
    public function user_can_complete_a_survey()
    {
        $this->actingAs($user = factory('App\User')->create());

        $this->scaleQuestion->submittable->updateScale([
            'minimum' => 1,
            'maximum' => 10
        ]);

        $this->postJson(route('completions.store', ['survey' => $this->survey]), [
            'answers' => $this->validAnswerData()
            ])->assertStatus(200);

        $this->assertCount(1, $this->survey->completions);
        $this->assertCount(3, $this->survey->completions->first()->answers);
    }

    /** @test */
    public function users_can_view_his_completion()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory('App\User')->create());

        $completion = factory('App\Completion')->create([
            'survey_id' => $this->survey->id,
            'user_id' => $user->id
        ]);

        $completion->addAnswers($this->validAnswerData());

        $this->get(route('completions.show', ['completion' => $completion]))->assertStatus(200);
    }

    protected function validAnswerData()
    {
        return [
            [
                'question_id' => $this->multipleChoiceQuestion->id,
                'text' => 'foo'
            ],
            [
                'question_id' => $this->openQuestion->id,
                'text' => 'foobar'
            ],
            [
                'question_id' => $this->scaleQuestion->id,
                'text' => $this->scaleQuestion->submittable->minimum
            ]
        ];
    }
}
