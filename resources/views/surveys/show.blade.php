<h2>{{ $survey->title}}</h2>
<form action="{{route('completions.store', ['survey' => $survey])}}" method="POST">
    {{ csrf_field()}}
    @foreach($survey->questions as $index => $question )
     <ul>
         <li>{{ $question->title}}</li>
         @include("questions._{$question->submitType}", [
            'index' => $index
         ]);
     </ul>
@endforeach
<button type="submit">Submit</button>
</form>
