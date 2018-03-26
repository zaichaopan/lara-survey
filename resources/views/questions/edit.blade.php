@extends('layouts.app')
@section('content')

<nav class="my-8 rounded font-sans w-full">
    <ol class="list-reset flex text-grey-dark">
    <li><a href="{{route('surveys.show', $question->survey)}}" class="text-blue font-bold">Back</a></li>
        <li><span class="mx-2">/</span></li>
        <li>Edit Quesiton</li>
    </ol>
</nav>

<div class="my-6">
    Switch To:
    @foreach($question->alternativeTypes() as $type)
    <a href="{{route('questions.types.edit', ['question' => $question, 'type' => $type])}}"
       class="border border-grey-dark px-4 py-2 no-underline rounded-full text-grey-darker font-hairline mr-2">
       {{ucfirst_str_replace('_', ' ', $type) }}
    </a>
    @endforeach
</div>

<form action="{{route('surveys.questions.update', ['survey' =>$survey, 'question' => $question])}}" method="POST">
    {{ method_field('PATCH') }}
    @include('questions._form')
</form>
@endsection
