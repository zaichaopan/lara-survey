@extends('layouts.app')
@section('content')
    @include('shared._errors')
    <div>
        <p><a href="{{route('surveys.show', ['survey' => $survey])}}">back</a>/Send Invitation:</p>
    </div>

    <form class="mt-8" action="{{route('surveys.invitations.store', ['surveys' => $survey])}}" Method="POST">
        {{ csrf_field() }}

        <div class="mb-6">
            <label class="block text-grey-darker text-sm font-bold mb-2" for="email">Recipient Email:</label>
            <input type="email" class="appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3" id="email" name="email" value="{{ old('email')}}" required>
        </div>

        <div class="mb-6">
            <label class="block text-grey-darker text-sm font-bold mb-2" for="message">Message:</label>
            <textarea class="appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3" rows="5" id="message" name="message" required></textarea>
        </div>

        <button type="submit" class="py-2 px-4 border border-grey rounded-full font-hairline">Submit</button>
    </form>
@endsection
