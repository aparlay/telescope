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
        if(!config('app.is_testing')) {
            $recaptChaRule = ['required', 'recaptcha'];
        } else {
            $recaptChaRule = ['nullable'];
        }

        return [
            'topic' => ['required', 'string'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string'],
            'message' => ['required', 'string'],
            'g-recaptcha-response' => $recaptChaRule,
        ];
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.recaptcha' => 'reCaptcha validation failed',
        ];
    }
}
