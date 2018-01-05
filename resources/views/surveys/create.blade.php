@extends('layouts.app')
@section('content')
<form action="{{route('surveys.store')}}" method="POST">
    {{ csrf_field() }}
    <h2>Title:</h2>
    <input type="text" name="title">
    <button type="submit">Create</button>
</form>
@endsection
