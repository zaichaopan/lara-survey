<?php

namespace Tests\Unit;

use App\Option;
use App\Survey;
use App\Question;
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
        $multipleChoiceQuestion = factory('App\Question')->states('multiple_choice')->create();
        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $multipleChoiceQuestion->submittable);

        $scaleQuestion = factory('App\Question')->states('scale')->create();
        $this->assertInstanceOf(ScaleSubmittable::class, $scaleQuestion->submittable);

        $openQuestion = factory('App\Question')->states('open')->create();
        $this->assertInstanceOf(OpenSubmittable::class, $openQuestion->submittable);
    }

    /** @test */
    public function it_adds_options()
    {
        $question = factory('App\Question')->states('multiple_choice')->create();
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
        $multipleChoiceQuestion = createMultipleChoiceQuestion();
        $newQuestionAttributes = [
            'title' => 'new title',
            'options' => ['new foo', 'new bar', 'new baz']
        ];
        $multipleChoiceQuestion = $multipleChoiceQuestion->updateAttributes($newQuestionAttributes);
        $this->assertEquals('new title', $multipleChoiceQuestion->title);
        $this->assertEquals(['new foo', 'new bar', 'new baz'], $multipleChoiceQuestion->options->pluck('text')->all());

        $scaleSubmittable = factory('App\ScaleSubmittable')->create(['minimum' => 0, 'maximum' => 5]);
        $scaleQuestion = factory('App\Question')->states('scale')->create(['submittable_id' => $scaleSubmittable->id]);
        $newQuestionAttributes = ['title' => 'new title', 'minimum' => 1, 'maximum' => 10 ];
        $scaleQuestion = $scaleQuestion->updateAttributes($newQuestionAttributes);
        $this->assertEquals('new title', $scaleQuestion->title);
        $this->assertEquals(1, $scaleQuestion->submittable->minimum);
        $this->assertEquals(10, $scaleQuestion->submittable->maximum);

        $openQuestion = factory('App\Question')->states('open')->create();
        $this->assertNotEquals('new title', $openQuestion->title);
        $newQuestionAttributes = ['title' => 'new title',];
        $openQuestion = $openQuestion->updateAttributes($newQuestionAttributes);
        $this->assertEquals('new title', $openQuestion->title);
    }

    /** @test */
    public function it_can_build_attributes_for_a_submittable_type()
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

    /** @test */
    public function it_can_delete_options()
    {
        $question = createMultipleChoiceQuestion();
        $this->assertCount(3, $question->fresh()->options);
        $question->deleteOptions();
        $this->assertCount(0, $question->fresh()->options);
        $this->assertEquals(0, Option::count());
    }

    /** @test */
    public function it_can_dissociate_submittable_type()
    {
        $question = factory('App\Question')->states('multiple_choice')->create();
        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $question->submittable);
        $question->dissociateType();
        $this->assertNull($question->submittable);

        $question = factory('App\Question')->states('scale')->create();
        $this->assertInstanceOf(ScaleSubmittable::class, $question->submittable);
        $question->dissociateType();
        $this->assertNull($question->submittable);

        $question = factory('App\Question')->states('open')->create();
        $this->assertInstanceOf(OpenSubmittable::class, $question->submittable);
        $question->dissociateType();
        $this->assertNull($question->submittable);
    }

    /** @test */
    public function it_can_switch_type()
    {
        $question = createMultipleChoiceQuestion();
        $question->switchType(['submittable_type' => 'open_submittable']);
        $question = $question->fresh();
        $this->assertInstanceOf(OpenSubmittable::class, $question->submittable);
        $this->assertEquals(0, Option::count());

        $question->switchType([
            'submittable_type' => 'scale_submittable',
            'minimum' => 1,
            'maximum' => 10
        ]);
        $question = $question->fresh();
        $this->assertInstanceOf(ScaleSubmittable::class, $question->submittable);
        $this->assertEquals(1, $question->submittable->minimum);
        $this->assertEquals(10, $question->submittable->maximum);

        $question->switchType([
            'submittable_type' => 'multiple_choice_submittable',
            'options' => ['foo', 'bar', 'baz']
        ]);
        $question = $question->fresh();
        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $question->submittable);
        $this->assertEquals(['foo', 'bar', 'baz'], $question->options->pluck('text')->all());
    }

    /** @test */
    public function it_can_find_option_by_text()
    {
        $question = createMultipleChoiceQuestion();
        $text =  $question->options->first()->text;
        $invalidText = "invalid{$text}";
        $this->assertNotNull($question->findOptionByText($text));
        $this->assertNull($question->findOptionByText($invalidText));
    }
}
