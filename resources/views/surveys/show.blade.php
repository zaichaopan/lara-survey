@extends('layouts.app')
@section('content')



<div class="survey-info text-center py-8">
    <h1 class="text-grey-darker">{{ $survey->title}}</h1>
</div>
<div>
    <div class="my-4 text-center">
        @foreach($survey->availableSubmittableTypes() as $submittableType)
            <a href="{{route('surveys.questions.create', ['survey' => $survey, 'submittable_type' => $submittableType])}}" class="border border-grey-dark px-4 py-2 no-underline rounded-full text-grey-darker font-hairline mr-2">
              &#x0002B; {{ ucfirst_str_replace('_', ' ', $submittableType) }} question
            </a>
        @endforeach
    </div>

    <div class="text-center p-4">
        <a class="border border-grey-dark px-4 py-2 no-underline rounded-full text-grey-darker font-hairline mr-2" href="{{route('surveys.invitations.create', ['survey' => $survey])}}">
                    Invite Participants
                </a>
        <a class="border border-grey-dark px-4 py-2 no-underline rounded-full text-grey-darker font-hairline mr-2" href="{{route('surveys.summaries.show', ['survey' => $survey])}}">
                View Summaries
                </a>
    </div>
</div>

@foreach($survey->questions as $index => $question )
<ul class="list-reset">
    <li class="pt-8">{{ $index+1}}. {{ $question->title}}</li>
    @include("{$question->submittableType()}s._show")
    <a href="{{route('surveys.questions.edit', ['survey'=> $survey, 'question' => $question])}}">Edit</a>
</ul>

@endforeach
@endsection
