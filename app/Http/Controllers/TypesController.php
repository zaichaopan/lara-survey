<?php

namespace App\Http\Controllers;

use App\Question;
use App\Submittable;
use Illuminate\Validation\Rule;

class TypesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:update,question');
    }

    public function edit(Question $question, $submittableType)
    {
        $question = $question->buildAttributes($submittableType);
        return view('types.create', compact('question'));
    }

    public function store(Question $question)
    {
        $question->switchType($this->questionAttributes());

        return redirect(route('surveys.show', ['survey' => $question->survey]));
    }

    protected function questionAttributes()
    {
        return request()->validate([
            'submittable_type' => ['required', Rule::in(Submittable::available())],
            'options' => 'options',
            'minimum' => 'minscale',
            'maximum' => 'maxscale'
        ]);
    }
}
