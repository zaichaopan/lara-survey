<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Survey;
use App\Completion;
use Illuminate\Http\Request;

class CompletionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Survey $survey)
    {
        $user = auth()->user();
        abort_if($user->hasCompleted($survey), 400);
        $completion = $survey->completeBy($user, $this->answersAttributes());
        return redirect(route('completions.show', ['completion' => $completion]));
    }

    public function show(Completion $completion)
    {
        return view('completions.show', compact('completion'));
    }

    protected function answersAttributes()
    {
        return request()->validate([
            'answers_attributes' => 'required',
            'answers_attributes.*.question_id' => 'required|integer',
            'answers_attributes.*.text' => 'nullable'
        ]);
    }
}
