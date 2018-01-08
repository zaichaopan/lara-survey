@extends('layouts.app')
@section('content')
    @include('shared._errors')
    <form action="{{route('surveys.completions.store', ['survey' => $survey])}}" method="POST">
        {{ csrf_field()}}

        <input type="text" name="token" value="{{$invitation->token}}" hidden>
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
