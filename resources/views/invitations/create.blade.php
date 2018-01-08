@extends('layouts.app')
@section('content')
    @include('shared._errors')
    <div>
        <p><a href="{{route('surveys.show', ['survey' => $survey])}}">back</a>/Send Invitation:</p>
    </div>

    <form class="form" action="{{route('surveys.invitations.store', ['surveys' => $survey])}}" Method="POST">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="email">Recipient Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email')}}">
        </div>

        <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" rows="5" id="message" name="message"></textarea>
        </div>

        <button type="submit" class="btn btn-default">Submit</button>
    </form>
@endsection
