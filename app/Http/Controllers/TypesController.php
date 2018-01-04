<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Validation\Rule;

class TypesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:update,question');
    }

    public function create(Question $question)
    {
        $submittableType = request('submittable_type');
        abort_unless(in_array($submittableType, Question::SUBMITTABLE_TYPES), 404);
        $question = $question->buildAttributes($submittableType);
        return view('types.create', compact('question'));
    }

    public function store(Question $question)
    {
        $question->switchType($this->questionAttributes());
        return redirect(route('surveys.show', ['survey' => $question->survey ]));
    }

    protected function questionAttributes()
    {
        return request()->validate([
            'submittable_type' => ['required', Rule::in(Question::SUBMITTABLE_TYPES)],
            'options' => 'options',
            'minimum' => 'minscale',
            'maximum' => 'maxscale'
        ]);
    }
}
