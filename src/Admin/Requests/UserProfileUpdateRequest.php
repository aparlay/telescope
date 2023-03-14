<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\Setting;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use MongoDB\BSON\ObjectId;

class UserProfileUpdateRequest extends FormRequest
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
            'user_id' => ['required'],
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($this->user_id, '_id'),
                'min:3',
                'max:30',
                'alpha_dash',
            ],
            'bio' => ['nullable', 'string'],
            'promo_link' => ['nullable', 'url'],
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

    public function prepareForValidation()
    {
        $this->merge(['user_id' => request()->has('user_id') ? new ObjectId(request()->input('user_id')) : null]);
    }
}
