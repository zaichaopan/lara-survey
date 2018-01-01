<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Survey;

class SurveysController extends Controller
{
    public function show(Survey $survey)
    {
        return view('surveys.show', compact('survey'));
    }
}
