<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Support\Str;

class ContactUsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (! config('app.is_testing')) {
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


    /**
     * This function is responsible to perform pre-validation.
     */
    public function prepareForValidation()
    {
        /* Convert uppercase email charecter into lowercase */
        $this->email = Str::of($this->email)->trim()->lower()->toString();
    }
}
