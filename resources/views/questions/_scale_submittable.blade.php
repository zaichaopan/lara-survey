<input type="text" name="answers[{{$index}}]['question_id']" value="{{$question->id}}" hidden>

@foreach(range($question->submittable->minimum, $question->submittable->maximum) as $number)
  <input type="radio" name="answers[{{$index}}]['text']" value="{{$number}}">{{$number}}<br>
@endforeach
