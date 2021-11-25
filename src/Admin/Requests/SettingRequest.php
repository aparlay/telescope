<?php

namespace Aparlay\Core\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'group' => 'required',
            'title' => 'required',
            'value' => [
                'required',
                function($attribute, $value, $fail) {
                    if($this->type === 'json') {
                        if(!json_decode($value)) {
                            $fail('Value is not a valid json.');
                        }
                    }
                }
            ]
        ];
    }
}
