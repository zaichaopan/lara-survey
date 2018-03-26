@extends('layouts.app')

@section('content')

<nav class="my-8 rounded font-sans w-full">
  <ol class="list-reset flex text-grey-dark">
    <li><a href="{{route('surveys.show', $question->survey)}}" class="text-blue font-bold">Back</a></li>
    <li><span class="mx-2">/</span></li>
    <li>Edit Quesiton</li>
  </ol>
</nav>

<p class="my-2">{{ $question->title }}</p>


<form action="{{route('questions.types.store', ['$question' =>$question])}}" method="POST">

  {{ csrf_field() }}
 <input type="text" name="submittable_type"
        value="{{ $question->submittableType() }}" hidden>

  @include("{$question->submittableType()}s._form")

  <button type="submit" class="border border-grey-darker py-2 px-4 rounded-full text-grey-darker">Submit</button>
</form>
@endsection
