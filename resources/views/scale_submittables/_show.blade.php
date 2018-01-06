@foreach(range($question->submittable->minimum, $question->submittable->maximum) as $option)
    <div class="radio">
     <label class="checkbox-inline">
        <input type="radio" name="answers_attributes[{{$question->id}}][text]" value="{{$option}}" required>{{$option}}
    </label>
    </div>
@endforeach

