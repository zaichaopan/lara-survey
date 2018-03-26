@extends('layouts.app')
@section('content')
<p class="py-4"><a href="{{route('surveys.show', ['survey' => $survey])}}">back</a>/Add Question:</p>
 @include('shared._errors')
<form class="form-horizontal col-md-6" action="{{route('surveys.questions.store', ['survey' => $survey])}}" method="POST">
    @include('questions._form')
</form>
@endsection
