<?php

namespace App\Http\Controllers;

use App\Survey;

class SurveysController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $surveys = Survey::latest()->get();
        return view('surveys.index', compact('surveys'));
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
