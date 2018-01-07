<?php

namespace App\Http\Controllers;

use App\Survey;


class InvitationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:update,survey');
    }

    public function create(Survey $survey)
    {
        return view('invitations.create', compact('survey'));
    }

    public function store(Survey $survey)
    {
        $validatedData = request()->validate([
            'email' => 'required|email',
            'message' => 'required'
        ]);

        $survey->sendInvitation($validatedData);
        return back()->with('message', 'Invitation has been sent!');
    }
}
