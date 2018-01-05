@foreach(range($question->submittable->minimum, $question->submittable->maximum) as $option)
    <div class="radio">
     <input name="answers_attributes[{{$question->id}}][question_id]" type="text" value="{{$question->id}}" hidden/>
     <label class="checkbox-inline">
        <input type="radio" name="answers_attributes[{{$question->id}}][text]" value="{{$option}}" required>{{$option}}
    </label>
    </div>
@endforeach

