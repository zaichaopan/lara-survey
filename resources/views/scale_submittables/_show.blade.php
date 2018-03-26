@foreach(range($question->submittable->minimum, $question->submittable->maximum) as $option)
    <div class="radio py-1">
     <label class="checkbox-inline">
        <input type="radio" class="mr-2" name="answers_attributes[{{$question->id}}][text]" value="{{$option}}" required>{{$option}}
    </label>
    </div>
@endforeach

