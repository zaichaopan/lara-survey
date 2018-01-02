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
        $question = factory('App\Question')->create();
        $multipleChoiceSubmittable = new MultipleChoiceSubmittable;
        $question = $question->associateType($multipleChoiceSubmittable);
        $this->assertInstanceOf(MultipleChoiceSubmittable::class, $question->submittable);

        $question = factory('App\Question')->create();
        $scaleSubmittable = new ScaleSubmittable;
        $question = $question->associateType($scaleSubmittable);
        $this->assertInstanceOf(ScaleSubmittable::class, $question->submittable);

        $question = factory('App\Question')->create();
        $openSubmittable = new OpenSubmittable;
        $question = $question->associateType($openSubmittable);
        $this->assertInstanceOf(OpenSubmittable::class, $question->submittable);
    }

    /** @test */
    public function it_adds_options()
    {
        $question = factory('App\Question')->create();
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
        $question = factory('App\Question')->create([
            'title' =>'hello world'
        ]);

        $multipleChoiceSubmittable = new MultipleChoiceSubmittable;
        $multipleChoiceSubmittable->save();
        $question = $question->associateType($multipleChoiceSubmittable);

        $option = $question->addOptions([
            new Option(['text' => 'foo']),
            new Option(['text' => 'bar']),
            new Option(['text' => 'baz']),
        ]);

        $newQuetionAttributes = [
            'title' => 'new title',
            'options' => [
                'new foo', 'new bar', 'new baz'
            ]
        ];

        $question = $question->updateAttributes($newQuetionAttributes);
        $this->assertEquals('new title', $question->title);
        $this->assertEquals(['new foo', 'new bar', 'new baz'], $question->options->pluck('text')->all());


        $scaleQuestion = factory('App\Question')->create(['title' =>'hello world']);
        $scaleSubmittable = new ScaleSubmittable;
        $scaleSubmittable->save();
        $scaleQuestion = $scaleQuestion->associateType($scaleSubmittable);
        $newQuetionAttributes = [
            'title' => 'new title',
            'minimum' => 1,
            'maximum' => 10
        ];
        $scaleQuestion = $scaleQuestion->updateAttributes($newQuetionAttributes);
        $this->assertEquals('new title', $scaleQuestion->title);
        $this->assertEquals(1, $scaleQuestion->submittable->minimum);
        $this->assertEquals(10, $scaleQuestion->submittable->maximum);


        $openQuestion = factory('App\Question')->create(['title' =>'hello world']);
        $openSubmittable = new OpenSubmittable;
        $openSubmittable->save();
        $openQuestion = $openQuestion->associateType($openSubmittable);
        $newQuetionAttributes = [
            'title' => 'new title',
        ];
        $openQuestion = $openQuestion->updateAttributes($newQuetionAttributes);
        $this->assertEquals('new title', $openQuestion->title);
    }

    /** @test */
    public function it_can_build_attrubtes_for_a_submittable_type()
    {
        $question = new Question;
        $question = $question->buildAttributes('multiple_choice_submittable');
        $this->assertEquals(null, $question->title);
        $this->assertCount(3, $question->options);
        $this->assertEquals('App\MultipleChoiceSubmittable', $question->submittable_type);

        $scaleQuestion = new Question;
        $scaleQuestion = $scaleQuestion->buildAttributes('scale_submittable');
        $this->assertEquals(null, $question->title);
        $this->assertEquals('App\ScaleSubmittable', $scaleQuestion->submittable_type);
        $this->assertEquals(0, $scaleQuestion->submittable->minimum);
        $this->assertEquals(1, $scaleQuestion->submittable->maximum);

        $openQuestion = new Question;
        $openQuestion = $openQuestion->buildAttributes('open_submittable');
        $this->assertEquals(null, $openQuestion->title);
        $this->assertEquals('App\OpenSubmittable', $openQuestion->submittable_type);
        $this->assertInstanceOf(OpenSubmittable::class, $openQuestion->submittable);
    }
}
