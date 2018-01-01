<form actions="{{route('surveys.create')}}" actions="POST">
    {{ csrf_field() }}
    <h2>Title:</h2>
    <input type="text" name="title">
    <button type="submit">Create</button>
</form>
