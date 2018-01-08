@extends('layouts.app')
@section('content')
    <h1>{{ $summary->survey->title}}</h1>
    <h2>Summary: {{$summary->questionsCount}} questions,  {{$summary->completionsCount}} completions</h2>
     @foreach($summary->questions as $question)
        <h3>{{$question->title}}</h3>
         @include("{$question->submittableType()}s._summary")
     @endforeach
@endsection
