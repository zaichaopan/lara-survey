@extends('layouts.app')

@section('content')
    <a href="{{route('surveys.create')}}">New Survey</a>
    @forelse ($surveys as $survey)
        <li class="list-group-item"><a href="{{route('surveys.show', ['survey' => $survey])}}">{{ $survey->title }}</a></li>
    @empty
    <p>No surveys</p>
    @endforelse
@endsection
