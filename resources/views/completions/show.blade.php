@foreach($completion->answers as $index=> $answer)
    <ul>
       <li>{{$answer->question->title}}</li>
        @include("questions._{$answer->question->submitType}", [
            'question' => $answer->question,
            'answer' => $answer,
            'index' => $index
        ])
    </ul>
@endforeach
