<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'g-recaptcha-response' => [Rule::excludeIf(config('app.is_testing')), 'required', 'recaptcha'],
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
