<h1>Add Question:</h1>
<form action="route('questions.store', ['survey' =>$survey])" method="POST">
    {{ csrf_field() }}
    <input type="text" name="question_submittable_type" value="{{$questionSubmittableType}}" hidden>

    <label>Title</label>
    <input type="text" name="title" placeholder="Please enter your question"><br>

    @include("questions._{$questionSubmittableType}_form");
    <button type="submit">Submit</button>
</form>
