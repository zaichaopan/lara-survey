<?php

namespace App\Http\Controllers;

use App\Survey;

class SummariesController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function show(Survey $survey, $summaryStrategy)
    {
        $summary = $survey->summary($summaryStrategy);
        return view('summaries.show', compact('summary'));
    }
}
