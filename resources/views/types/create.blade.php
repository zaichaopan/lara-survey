<h1>{{ $question->title }}</h1>
<form action="route('questions.type.store', ['survey' =>$survey])" method="POST">

 <input type="text" name="question_submittable_type"
        value="{{ $question->submittableType() }}" hidden>

  @include("{$question->submittableType()}s._form")

  <button type="submit">Submit</button>
</form>
