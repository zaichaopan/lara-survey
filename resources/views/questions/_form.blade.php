    {{ csrf_field() }}

    <input type="text" name="question_submittable_type"
        value="{{ $question->submittableType() }}" hidden>

    <label>Title</label>
    <input type="text" name="title" value="{{$question->title}}"><br>

    @include("questions._{$question->submittableType()}"_);
    <button type="submit">Submit</button>

