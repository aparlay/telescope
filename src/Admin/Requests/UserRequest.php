<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
        switch ($this->method()) {
            case 'PATCH':
                return [
                    'status' => [
                        'required',
                        Rule::in(array_keys(User::getStatuses())),
                    ],
                ];
            case 'PUT':
                return [
                    'email' => ['required',
                        'email',
                        Rule::unique('users', 'email')->ignore($this->user->_id, '_id'),
                        'max:255',
                    ],
                    'username' => ['required',
                        Rule::unique('users', 'username')->ignore($this->user->_id, '_id'),
                        'max:255',
                    ],
                    'phone_number' => ['nullable', 'numeric', 'digits:10', 'unique:users'],
                    'gender' => [Rule::in(array_keys(User::getGenders()))],
                    'type' => [Rule::in(array_keys(User::getTypes())), 'integer'],
                ];
            default: break;
        }
    }

    /**
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors(); // Here is your array of errors

        throw new HttpResponseException(
            redirect()->back()->withErrors($errors)
        );
    }
}
