<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Note;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NoteRequest extends FormRequest
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
            'message' => 'required',
            'type' => ['required',
                Rule::in(array_keys(Note::getTypes())),
            ],

        ];
    }
}
