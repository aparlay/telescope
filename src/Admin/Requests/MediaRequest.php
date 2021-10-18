<?php

namespace Aparlay\Core\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
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
            'skin_score' => ['required', 'integer'],
            'awesomeness_score' => ['required', 'integer'],
        ];
    }
}
