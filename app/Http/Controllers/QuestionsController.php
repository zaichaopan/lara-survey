<?php

namespace App\Http\Controllers;

use App\Survey;
use App\Question;
use App\SubmitType;
use App\ScaleSubmittable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\QuestionForm;

class QuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Survey $survey)
    {
        $this->authorize('update', $survey);
        //abort_unless(in_array($submittableType, static::SUBMITTABLE_TYPES), 404);
        $question = (new Question)->buildAttributes(request('question_submittable_type'));
        return view('questions.create', compact('question', 'survey'));
    }

    public function edit(Survey $survey, Question $question)
    {
        $this->authorize('update', $survey);
        return view('questions.edit', compact('question', 'survey'));
    }

    public function store(Survey $survey, QuestionForm $questionForm)
    {
        $this->authorize('update', $survey);
        $survey->addQuestion($questionForm->data());
        return redirect(route('surveys.show', compact('survey')));
    }

    public function update(Survey $survey, Question $question, QuestionForm $questionForm)
    {
        $this->authorize('update', $survey);
        $question->updateAttributes($questionForm->data());
        return redirect(route('surveys.show', compact('survey')));
    }
}
