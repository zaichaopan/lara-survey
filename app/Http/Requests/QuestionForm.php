<?php

namespace App\Http\Requests;

use App\Question;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Submittable;

class QuestionForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'submittable_type' => ['required', Rule::in(Submittable::acceptTypes())],
            'options' => 'options',
            'minimum' => 'minscale',
            'maximum' => 'maxscale',
        ];
    }

    public function data()
    {
        return $this->only([
            'title',
            'submittable_type',
            'options',
            'minimum',
            'maximum'
        ]);
    }
}
