<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Survey;
use Illuminate\Http\Request;

class CompletionsController extends Controller
{
    public function store(Survey $survey)
    {
        $copmletion = $survey->completedBy(auth()->user());
        var_dump(request('answers'));
        $answers = $collection->buildAnswerAttributes(request('answers'));
        $collection->addAnswers($answers);
    }
}
