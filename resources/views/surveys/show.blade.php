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
        @if(auth()->user()->hasCompleted($survey))
        &#124
        <a href="{{route('surveys.summaries.show', ['survey'=>$survey, 'summary' => 'user_answer'])}}">
            Your Answers
        </a>
        @endif
    </div>
    <hr>

    <form action="{{route('surveys.completions.store', ['survey' => $survey])}}" method="POST">
        {{ csrf_field()}}
        @foreach($survey->questions as $index => $question )
         <ul>
             <li>{{ $index+1}}. {{ $question->title}}</li>
             @include("{$question->submittableType()}s._show")
         </ul>
         @endforeach

         <div class="form-group">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
