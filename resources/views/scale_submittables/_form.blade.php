<div class="form-group">
    <div class="mb-6">
        <label class="block text-grey-darker text-sm font-bold mb-2" for="minimum">
            Minimum
          </label>
        <input class="appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3"
               id="minimum"
               type="number"
               name="minimum"
               step="1"
               min="0"
               value="{{ old('minimum') ?? $question->submittable->minimum}}"
               placeholder="Enter the the minimum scale number"
               required>
    </div>

    <div class="mb-6">
        <label class="block text-grey-darker text-sm font-bold mb-2" for="maximum">
                Maximum
              </label>
        <input class="appearance-none border rounded w-full py-2 px-3 text-grey-darker mb-3"
               id="maximum"
               type="number"
               name="maximum"
               step="1"
               min="1"
               placeholder="Enter the the maximum scale number"
               value="{{ old('maximum') ?? $question->submittable->maximum}}"
               required>
    </div>
</div>

