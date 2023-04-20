<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'type' => [
                Rule::in(array_keys(Setting::getValueTypes())),
            ],
            'value' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($this->type === 'json') {
                        if (!json_decode($value)) {
                            $fail('Value is not a valid json.');
                        }
                    }
                },
            ],
        ];
    }

    public function prepareForValidation()
    {
        if ((int) $this->type == Setting::VALUE_TYPE_BOOLEAN) {
            $this->merge(['value' => request()->has('value')]);
        }
    }
}
