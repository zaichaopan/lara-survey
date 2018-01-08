@extends('layouts.app')
@section('content')
    <div>
        Thank you very much for your participation! The following is your answer?
    </div>
    @foreach($completion->answers as $index=> $answer)
        <ul>
           <li>
                <strong>{{$answer->question->title}}</strong>
                <p>Your answer: {{$answer->text}}</p>
            </li>
        </ul>
    @endforeach
@endsection
