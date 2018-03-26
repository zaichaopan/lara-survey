@foreach($question->options as $key => $option)
    <div class="mb-6">
    <label class="block text-grey-darker text-sm font-bold mb-2" for="{{'option'. $key}}">
           Option:{{ ++$key}}
          </label>
        <input class="ppearance-none border rounded w-full p-2 text-grey-darker mb-3"
               type="text"
               id="{{'option' . $key}}"
               name="options[]"
               value="{{$option->text}}"
               placeholder="Enter option text here!"
               required>

    </div>
@endforeach


