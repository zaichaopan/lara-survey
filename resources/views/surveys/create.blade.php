<form actions="{{route('surveys.create')}}" actions="POST">
    {{ csrf_field() }}
    <h2>Please enter a title for your survey:</h2>
    <input type="text" name="title">
    <button type="submit">Create</button>
</form>
