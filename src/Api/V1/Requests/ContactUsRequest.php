<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Support\Facades\Request;

class ContactUsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'topic' => ['required', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string'],
            'message' => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ];
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.recaptcha' => 'reCaptcha validation failed',
        ];
    }
}
