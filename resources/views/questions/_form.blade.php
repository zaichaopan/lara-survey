{{ csrf_field() }}
<input type="text" name="submittable_type" value="{{ $question->submittableType() }}" hidden>

<div class="mb-4">
    <label class="block text-grey-darker text-sm font-bold mb-2" for="username">Title</label>
    <input class="appearance-none border rounded w-full py-2 px-3 text-grey-darker"
           id="title"
           name="title"
           type="text"
           placeholder="Title"
           value="{{old('title') ?? $question->title}}" placeholder="title"
           required>
</div>

@include("{$question->submittableType()}s._form")

<div class="mt-2">
    <button class="bg-blue border-blue rounded-full text-sm border-4 text-white px-4 py-2 font-hairline" type="submit">
          Submit
    </button>
</div>
