<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Survey;
use App\SubmitType;
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
        abort_unless(in_array($questionSubmittableType = request('question_submittable_type'), Question::SUBMITTABLE_TYPES), 404);
        return view('questions.create', compact('questionSubmittableType', 'survey'));
    }

    public function store(Survey $survey)
    {
        $this->authorize('update', $survey);

        request()->validate([
            'title' => 'required',
            'question_submittable_type' => ['required', Rule::in(Question::SUBMITTABLE_TYPES)],
         //   'options' => 'options',
            'minimum' => 'minscale',
            'maximum' => 'maxscale',
        ]);

        $question = $survey->createQuestion(request(['title']))
            ->submitType(SubmitType::create($submittableType));

        return view('questions.edit', compact('survey', 'question'));
    }
}
