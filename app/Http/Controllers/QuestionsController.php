<?php

namespace App\Http\Controllers;

use App\Survey;
use App\Question;
use App\SubmitType;
use App\ScaleSubmittable;
use App\Http\Requests\QuestionForm;

class QuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:update,survey');
    }

    public function create(Survey $survey)
    {
        $submittableType = request('submittable_type');
        abort_unless(in_array($submittableType, Question::SUBMITTABLE_TYPES), 404);
        $question = (new Question)->buildAttributes($submittableType);
        return view('questions.create', compact('question', 'survey'));
    }

    public function edit(Survey $survey, Question $question)
    {
        return view('questions.edit', compact('question', 'survey'));
    }

    public function store(Survey $survey, QuestionForm $questionForm)
    {
        $survey->addQuestion($questionForm->data());
        return redirect(route('surveys.show', compact('survey')));
    }

    public function update(Survey $survey, Question $question, QuestionForm $questionForm)
    {
        $question->updateAttributes($questionForm->data());
        return redirect(route('surveys.show', compact('survey')));
    }
}
