<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Survey;
use App\Completion;
use Illuminate\Http\Request;

class CompletionsController extends Controller
{
    public function show(Completion $completion)
    {
        return view('completions.show', compact('completion'));
    }

    public function store(Survey $survey)
    {
        $completion = $survey->completedBy(auth()->user());
        $completion->addAnswers(request('answers'));
    }
}
