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
        $newQuestionAttributes = [
            'title' => 'new title',
            'options' => ['new foo', 'new bar', 'new baz']
        ];
        $multipleChoiceQuestion = $multipleChoiceQuestion->updateAttributes($newQuestionAttributes);
        $this->assertEquals('new title', $multipleChoiceQuestion->title);
        $this->assertEquals(['new foo', 'new bar', 'new baz'], $multipleChoiceQuestion->options->pluck('text')->all());

        $scaleQuestion = $this->createQuestion(ScaleSubmittable::class);
        $newQuestionAttributes = ['title' => 'new title', 'minimum' => 1, 'maximum' => 10 ];
        $scaleQuestion = $scaleQuestion->updateAttributes($newQuestionAttributes);
        $this->assertEquals('new title', $scaleQuestion->title);
        $this->assertEquals(1, $scaleQuestion->submittable->minimum);
        $this->assertEquals(10, $scaleQuestion->submittable->maximum);

        $openQuestion = $this->createQuestion(OpenSubmittable::class);
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
        $question = $this->createQuestion(MultipleChoiceSubmittable::class);
        $question->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar']),
            new Option(['text' => 'baz']),
        ]);

        $this->assertCount(3, $question->fresh()->options);
        $question->deleteOptions();
        $this->assertCount(0, $question->fresh()->options);
        $this->assertEquals(0, Option::count());
    }

    /** @test */
    public function it_can_dissociate_submittable_type()
    {
        $this->dissociateType(MultipleChoiceSubmittable::class);
        $this->dissociateType(OpenSubmittable::class);
        $this->dissociateType(ScaleSubmittable::class);
    }

    /** @test */
    public function it_can_switch_type()
    {
        $question = $this->createQuestion(MultipleChoiceSubmittable::class);

        $question->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar']),
            new Option(['text' => 'baz']),
        ]);

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
        $this->assertEquals([
            'foo', 'bar', 'baz'
        ], $question->options->pluck('text')->all());
    }

    /** @test */
    public function it_can_find_option_by_text()
    {
        $question = $this->createQuestion(MultipleChoiceSubmittable::class);
        $question->addOptions([new Option(['text' => 'foo'])]);
        $question = $question->fresh();
        $this->assertNotNull($question->findOptionByText('foo'));
        $this->assertNull($question->findOptionByText('foobar'));
    }

    protected function createQuestion($submittableClass)
    {
        $question = factory('App\Question')->create();
        $submittable = new $submittableClass;
        $submittable->save();
        $question = $question->associateType($submittable);
        return $question;
    }

    protected function dissociateType($submittableTypeClass)
    {
        $question = $this->createQuestion($submittableTypeClass);
        $question = $question->fresh();
        $this->assertInstanceOf($submittableTypeClass, $question->submittable);
        $question->dissociateType();
        $this->assertNull($question->submittable);
    }
}
