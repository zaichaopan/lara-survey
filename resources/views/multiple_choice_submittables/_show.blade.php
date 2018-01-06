@foreach($question->options as $option)
   <div class="radio">
     <label class="checkbox-inline">
        <input type="radio" name="answers_attributes[{{$question->id}}][text]" value="{{$option->text}}" required>{{$option->text}}
    </label>
    </div>
@endforeach
