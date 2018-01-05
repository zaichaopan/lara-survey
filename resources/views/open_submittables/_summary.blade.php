 @foreach($question->summary() as $answer)
    <p>{{$answer->text }}</p>
 @endforeach
