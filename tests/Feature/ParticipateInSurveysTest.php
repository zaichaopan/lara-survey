<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInSurveysTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->survey = factory('App\Survey')->create();

        $this->multipleChoiceQuestion = factory('App\Question')
            ->states('multiple_choice')
            ->create(['survey_id' => $this->survey->id]);

        $this->openQuestion = factory('App\Question')
            ->states('open')
            ->create(['survey_id' => $this->survey->id]);

        $this->scaleQuestion = factory('App\Question')
            ->states('scale')
            ->create(['survey_id' => $this->survey->id]);
    }

    /** @test */
    public function use_can_view_survey_form()
    {
        $option1 = $this->multipleChoiceQuestion->addOption([
            'text' => 'foo'
        ]);

        $option2 = $this->multipleChoiceQuestion->addOption([
            'text' => 'bar'
        ]);

        $this->scaleQuestion->submittable->updateScale([
            'minimum' => 1,
            'maximum' => 10
        ]);

        $response = $this->get(route('surveys.show', ['survey' => $this->survey]))
            ->assertSee($this->survey->title)
            ->assertSee($this->multipleChoiceQuestion->title)
            ->assertSee($option1->text)
            ->assertSee($option2->text)
            ->assertSee($this->openQuestion->title)
            ->assertSee($this->scaleQuestion->title)
            ->assertSee((string)$this->scaleQuestion->submittable->minimum)
            ->assertSee((string)$this->scaleQuestion->submittable->maximum);
    }
}
