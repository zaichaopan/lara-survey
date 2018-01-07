<?php

namespace App\Http\Controllers;

use App\Survey;
use App\Completion;

class CompletionsController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function create(Survey $survey)
    {
    }

    public function store(Survey $survey)
    {
        abort_if(auth()->user()->hasCompleted($survey), 400);
        $completion = $survey->completeBy(auth()->user(), $this->answersAttributes($survey));
        return redirect(route('surveys.show', ['survey' => $survey]));
    }

    protected function answersAttributes($survey)
    {
        $size = count($survey->questions);
        $test = request()->validate(['answers_attributes' => 'required|array|size:' . $size]);
        return request('answers_attributes');
    }
}
