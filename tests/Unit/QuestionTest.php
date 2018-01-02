<?php

namespace Tests\Unit;

use App\Option;
use App\Survey;
use App\Question;
use App\SubmitType;
use Tests\TestCase;
use App\OpenSubmittable;
use App\ScaleSubmittable;
use App\MultipleChoiceSubmittable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    public function it_belongs_to_survey()
    {
        $question = factory('App\Question')->create();
        $this->assertInstanceOf(Survey::class, $question->survey);
    }

    /** @test */
    public function it_can_associate_to_submittable_type()
    {
        $multipleChoiceQuestion = $this->createQuestion(MultipleChoiceSubmittable::class);
        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $multipleChoiceQuestion->submittable);

        $scaleQuestion = $this->createQuestion(ScaleSubmittable::class);
        $this->assertInstanceOf(ScaleSubmittable::class, $scaleQuestion->submittable);

        $openQuestion = $this->createQuestion(OpenSubmittable::class);
        $this->assertInstanceOf(OpenSubmittable::class, $openQuestion->submittable);
    }

    /** @test */
    public function it_adds_options()
    {
        $question = $this->createQuestion(MultipleChoiceSubmittable::class);
        $option = $question->addOptions([ new Option(['text' => 'foo'])]);
        $this->assertTrue($question->options()->exists());
    }

    /** @test */
    public function it_get_submittable_type__snake_case_short_name()
    {
        $question = new Question(['submittable_type' => 'App\MultipleChoiceSubmittable']);
        $this->assertEquals('multiple_choice_submittable', $question->submittableType());
    }

    /** @test */
    public function it_can_update_attributes()
    {
        $multipleChoiceQuestion = $this->createQuestion(MultipleChoiceSubmittable::class);
        $multipleChoiceQuestion->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar']),
            new Option(['text' => 'baz']),
        ]);
        $newQuetionAttributes = [
            'title' => 'new title',
            'options' => ['new foo', 'new bar', 'new baz']
        ];
        $multipleChoiceQuestion = $multipleChoiceQuestion->updateAttributes($newQuetionAttributes);
        $this->assertEquals('new title', $multipleChoiceQuestion->title);
        $this->assertEquals(['new foo', 'new bar', 'new baz'], $multipleChoiceQuestion->options->pluck('text')->all());

        $scaleQuestion = $this->createQuestion(ScaleSubmittable::class);
        $newQuetionAttributes = ['title' => 'new title', 'minimum' => 1, 'maximum' => 10 ];
        $scaleQuestion = $scaleQuestion->updateAttributes($newQuetionAttributes);
        $this->assertEquals('new title', $scaleQuestion->title);
        $this->assertEquals(1, $scaleQuestion->submittable->minimum);
        $this->assertEquals(10, $scaleQuestion->submittable->maximum);

        $openQuestion = $this->createQuestion(OpenSubmittable::class);
        $newQuetionAttributes = ['title' => 'new title',];
        $openQuestion = $openQuestion->updateAttributes($newQuetionAttributes);
        $this->assertEquals('new title', $openQuestion->title);
    }

    /** @test */
    public function it_can_build_attrubtes_for_a_submittable_type()
    {
        $multipleChoiceQuestion = new Question;
        $multipleChoiceQuestion = $multipleChoiceQuestion->buildAttributes('multiple_choice_submittable');
        $this->assertEquals(null, $multipleChoiceQuestion->title);
        $this->assertCount(3, $multipleChoiceQuestion->options);
        $this->assertEquals('App\MultipleChoiceSubmittable', $multipleChoiceQuestion->submittable_type);

        $scaleQuestion = new Question;
        $scaleQuestion = $scaleQuestion->buildAttributes('scale_submittable');
        $this->assertEquals(null, $scaleQuestion->title);
        $this->assertEquals('App\ScaleSubmittable', $scaleQuestion->submittable_type);
        $this->assertEquals(0, $scaleQuestion->submittable->minimum);
        $this->assertEquals(1, $scaleQuestion->submittable->maximum);

        $openQuestion = new Question;
        $openQuestion = $openQuestion->buildAttributes('open_submittable');
        $this->assertEquals(null, $openQuestion->title);
        $this->assertEquals('App\OpenSubmittable', $openQuestion->submittable_type);
        $this->assertInstanceOf(OpenSubmittable::class, $openQuestion->submittable);
    }

    protected function createQuestion($submittableClass)
    {
        $question = factory('App\Question')->create();
        $submittable = new $submittableClass;
        $submittable->save();
        $question = $question->associateType($submittable);
        return $question;
    }
}
