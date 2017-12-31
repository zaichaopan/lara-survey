<?php

namespace Tests\Unit;

use App\SubmitType;
use Tests\TestCase;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_adds_multipe_choice_questions()
    {
        $this->createQuestion('multiple_choice', MultipleChoiceSubmittable::class);
    }

    /** @test */
    public function it_adds_open_questions()
    {
        $this->createQuestion('open', OpenSubmittable::class);
    }

    /** @test */
    public function it_adds_scale_questions()
    {
        $this->createQuestion('scale', ScaleSubmittable::class);
    }

    protected function createQuestion($type, $kclass)
    {
        $type = SubmitType::create($type);
        $survey = factory('App\Survey')->create();
        $question = $survey->createQuestion('foo')->submitType($type);
        $this->assertTrue($survey->questions->contains($question->id));
        $this->assertInstanceOf($kclass, $question->submittable);
    }
}
