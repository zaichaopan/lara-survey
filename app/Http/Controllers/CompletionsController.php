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
        $completion = $survey->completeBy(auth()->user())->addAnswers(request('answersAttributes'));
        return redirect(route('completions.show', ['completion' => $completion]));
    }

    public function show(Completion $completion)
    {
        return view('completions.show', compact('completion'));
    }
}
