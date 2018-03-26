@foreach($question->options as $option)
   <div class="radio py-1">
     <label>
        <input type="radio" class="mr-2" name="answers_attributes[{{$question->id}}][text]" value="{{$option->text}}" required>{{$option->text}}
    </label>
    </div>
@endforeach
