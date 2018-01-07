<?php

namespace App\Http\Controllers;

use App\Survey;
use App\Completion;

class CompletionsController extends Controller
{
    public function create(Survey $survey)
    {
        $invitation = $survey->findInvitationForToken(request('token'));
        return view('completions.create', compact('invitation', 'survey'));
    }

    public function show(Survey $survey, Completion $completion)
    {
        $invitation = $survey->findInvitationForToken(request('token'));
        return view('completions.show', compact('completion'));
    }

    public function store(Survey $survey)
    {
        $invitation = $survey->findInvitationForToken(request('token'));

        if ($invitation->completion) {
            return back()->with('message', 'You have already completed the survey!');
        }

        $completion = $survey->completeBy($invitation, $this->answersAttributes($survey));

        return redirect(route('surveys.completions.show', [
            'survey' => $survey,
            'completion' => $completion,
            'token' => requst('token')
        ]));
    }

    protected function answersAttributes($survey)
    {
        $size = count($survey->questions);
        $test = request()->validate(['answers_attributes' => 'required|array|size:' . $size]);
        return request('answers_attributes');
    }
}
