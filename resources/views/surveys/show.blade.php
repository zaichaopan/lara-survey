@extends('layouts.app')
@section('content')
    <div class="survey-info">
           <h2>{{ $survey->title}}</h2>
        <div>
           <span> by {{$survey->author->name}}</span>
        </div>
    </div>

    <div>
        <a href="{{route('surveys.questions.create', ['survey' => $survey, 'submittable_type' => 'multiple_choice_submittable'])}}">
            Add Multiple Choice Question
        </a> &#124
        <a href="{{route('surveys.questions.create', ['survey' => $survey, 'submittable_type' => 'scale_submittable'])}}">
            Add Scale Question
        </a> &#124
        <a href="{{route('surveys.questions.create', ['survey' => $survey, 'submittable_type' => 'open_submittable'])}}">
            Add Open Question
        </a>
        &#124
        <a href="{{route('surveys.invitations.create', ['survey' => $survey])}}">
            Invite
        </a>
         &#124

        <a href="{{route('surveys.summaries.show', ['survey' => $survey])}}">
        View Summaries
        </a>
    </div>
    <hr>

    @foreach($survey->questions as $index => $question )
     <ul>
         <li>{{ $index+1}}. {{ $question->title}}</li>
         @include("{$question->submittableType()}s._show")
     </ul>
     @endforeach

@endsection
