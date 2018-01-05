<?php

namespace App\Http\Controllers;

use App\Survey;
use Illuminate\Http\Request;

class SummariesController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function show(Survey $survey)
    {
        $summary = $survey->summary;
        return view('summaries.show', compact('summary'));
    }
}
