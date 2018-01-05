<h1>Add Question:</h1>
<form action="route('surveys.questions.store', ['survey' => $survey])" method="POST">
    @include('questions._form');
</form>
