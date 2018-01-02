<label>Option</label>
@foreach($question->options as $option)
    <label>Option</label>
    <input type="text" name="options[]" value="{{$option->text}}"><br>
@endforeach


