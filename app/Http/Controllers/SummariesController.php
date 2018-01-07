<?php

namespace App\Http\Controllers;

use App\Survey;

class SummariesController extends Controller
{
    public function __construct()
    {
        return $this->middleware('can:update,survey');
    }

    public function show(Survey $survey)
    {
        $summary = $survey->summary();
        return view('summaries.show', compact('summary'));
    }
}
