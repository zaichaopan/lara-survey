<?php

namespace App\Http\Controllers;

use App\Survey;
use App\Question;
use App\SubmitType;
use App\ScaleSubmittable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Survey $survey)
    {
        $this->authorize('update', $survey);
        $question = (new Question)->buildAttributes(request('question_submittable_type'));
        return view('questions.create', compact('question', 'survey'));
    }

    public function edit(Survey $survey, Question $question)
    {
        $this->authorize('update', $survey);
        return view('questions.edit', compact('question', 'survey'));
    }

    public function store(Survey $survey)
    {
        $this->authorize('update', $survey);

        $questionAttributes = request()->validate([
            'title' => 'required',
            'question_submittable_type' => ['required', Rule::in(Question::SUBMITTABLE_TYPES)],
            'options' => 'options',
            'minimum' => 'minscale',
            'maximum' => 'maxscale',
        ]);

        $survey->addQuestion($questionAttributes);

        return redirect(route('surveys.show', compact('survey')));
    }

    public function update(Survey $survey, Question $question)
    {
        $this->authorize('update', $survey);

        $questionAttributes = request()->validate([
            'title' => 'required',
            'question_submittable_type' => ['required', Rule::in(Question::SUBMITTABLE_TYPES)],
            'options' => 'options',
            'minimum' => 'minscale',
            'maximum' => 'maxscale',
        ]);

        $question->updateAttributes($questionAttributes);

        return redirect(route('surveys.show', compact('survey')));
    }
}
