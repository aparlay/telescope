<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Alert;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AuthRequest extends FormRequest
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
        $rules =  [
            'email' => ['required'],
            'password' => ['required'],
        ];

        if (!\App::environment('local')) {
            $rules['g-recaptcha-response'] = ['recaptcha'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.recaptcha' => 'reCaptcha validation failed',
        ];
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
