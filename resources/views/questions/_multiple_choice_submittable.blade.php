<input type="text" name="answers[{{$index}}]['question_id']" value="{{$question->id}}" hidden>

@foreach($question->options as $option)
  <input type="radio" name="answers[{{$index}}]['text']" value="{{$option->text}}" checked>{{$option->text}}<br>
@endforeach
