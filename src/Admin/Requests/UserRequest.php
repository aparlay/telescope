<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
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
            'email' => ['required',
                'unique:users, email,'. request()->route('user'),
                'max:255'
            ],
            'username' => ['required',
                'unique:users, username,'. request()->route('user'),
                'max:255'
            ],
            'phone_number' => ['nullable', 'numeric', 'digits:10', 'unique:users'],
            'gender' => [Rule::in(array_keys(User::getGenders()))],
            'type' => [Rule::in(array_keys(User::getTypes()))],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors(); // Here is your array of errors

        return back()->withErrors($errors);
    }
}
