@extends('layouts.app')
@section('content')
<form action="{{route('surveys.store')}}" method="POST">
    {{ csrf_field() }}

    <h1 class="mt-8 text-grey-darker font-hairline">Create a new survey: </h1>
    <div class="my-6">
        <input class="appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3"
               name="title"
               id="title"
               type="text"
               placeholder="Enter the survey title here!">
    </div>

    <button type="submit" class="py-2 px-4 border border-grey rounded-full font-hairline">Create</button>
</form>
@endsection
