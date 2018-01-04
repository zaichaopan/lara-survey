<?php

namespace App\Http\Controllers;

use App\Survey;
use App\Completion;

class CompletionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Survey $survey)
    {
        abort_if(auth()->user()->hasCompleted($survey), 400);
        $completion = $survey->completeBy(auth()->user(), $this->answersAttributes($survey));
        return redirect(route('completions.show', ['completion' => $completion]));
    }

    public function show(Completion $completion)
    {
        return view('completions.show', compact('completion'));
    }

    protected function answersAttributes($survey)
    {
        $size = count($survey->questions);

        request()->validate([
            'answers_attributes' => 'required|size:' . $size,
            'answers_attributes.*.question_id' => 'required|integer',
            'answers_attributes.*.text' => 'nullable'
        ]);

        return request('answers_attributes');
    }
}
