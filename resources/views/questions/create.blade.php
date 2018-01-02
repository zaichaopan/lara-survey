<h1>Add Question:</h1>
<form action="route('questions.store', ['survey' =>$survey])" method="POST">
    @include('questions._form');
</form>
