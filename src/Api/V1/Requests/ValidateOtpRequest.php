<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\RequiredIf;

/**
 * @property string email
 * @property string otp
 */
class ValidateOtpRequest extends FormRequest
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
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'otp' => ['required'],
            'device_id' => new RequiredIf($this->header('X-DEVICE-ID') == ''),
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'device_id.required' => 'Device Id cannot be blank.',
        ];
    }

    /**
     * This function is responsible to perform pre-validation.
     */
    public function prepareForValidation()
    {
        /* Convert uppercase email charecter into lowercase */
        $this->username = $this->email ? Str::lower($this->email) : $this->phone_number;
    }
}
