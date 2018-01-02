<h1>Edit Question:</h1>
<form action="route('questions.update', ['survey' =>$survey, 'question' => $question])" method="POST">
   {{ method_field('PATCH') }}
   @include('questions._form')
</form>
