@foreach($question->options as $option)
   <div class="form-group">
        <label class="control-label col-sm-2">Option:</label>
        <div class="col-sm-10">
            <input class="form-control input-sm" type="text" name="options[]" value="{{$option->text}}">
        </div>
    </div>
@endforeach


