<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Survey;

class SurveysController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $validatedData = request()->validate(['title' => 'required']);
        $survey = auth()->user()->addSurvey($validatedData);
        return view('surveys.show', compact('survey'));
    }
}
