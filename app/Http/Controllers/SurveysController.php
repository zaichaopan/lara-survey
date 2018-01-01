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

    public function create()
    {
        return view('surveys.create');
    }

    public function store()
    {
        $survey = auth()->user()->addSurvey(request([ 'title' ]));
        return view('surveys.show', compact('survey'));
    }
}
