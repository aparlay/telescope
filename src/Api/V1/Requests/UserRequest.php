<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [];
    }

    public function prepareForValidation()
    {
        if (strpos($this->username, '@') !== false) {
            $this->email = $this->username;
        }
        if ((int)$this->username > 100000) {
            $this->phone_number = $this->username;
        }
        $this->username = uniqid('', false);

        $this->merge([
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
        ]);
    }
}
