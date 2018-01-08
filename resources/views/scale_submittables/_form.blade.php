<div class="form-group">
    <label class="col-sm-2">Minimum:</label>
    <div class="col-sm-10">
        <input class="form-control input-sm"
            type="number"
            name="minimum"
            step="1"
            min="0"
            value="{{ old('minimum') ?? $question->submittable->minimum}}">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2">Maximum:</label>
    <div class="col-sm-10">
        <input class="form-control input-sm"
            type="number"
            name="maximum"
            step="1"
            min="1"
            value="{{ old('maximum') ?? $question->submittable->maximum}}">
    </div>
</div>
