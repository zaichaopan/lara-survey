{{ csrf_field() }}

<input type="text" name="submittable_type" value="{{ $question->submittableType() }}" hidden>

<div class="form-group">
    <label class="control-label col-sm-2">Title: </label>
    <div class="col-sm-10">
        <input class="form-control input-sm" type="text" name="title" value="{{old('title') ?? $question->title}}">
    </div>
</div>

@include("{$question->submittableType()}s._form")

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>

