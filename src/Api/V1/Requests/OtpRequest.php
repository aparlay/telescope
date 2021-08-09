<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\Otp;
use Aparlay\Core\Helpers\DT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @property string identity
 * @property string device_id
 */
class OtpRequest extends Request
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

    /**
     * This function is responsible to prepare the default value of OTP table columns
     */
    public function prepareForValidation()
    {
        /** Set the Default Values and required to be input parameters */
        $this->merge([
            'identity'      => $this->identity,
            'otp'           => (string)random_int(
                config('app.otp.length.min'),
                config('app.otp.length.max')
            ),
            'expired_at'    => DT::utcDateTime(['s' => config('app.otp.duration')]),
            'type'          => Str::contains($this->identity, '@') ? Otp::TYPE_EMAIL : Otp::TYPE_SMS,
            'device_id'     => $this->device_id,
            'incorrect'     => 0,
            'validated'     => false
        ]);
    }
}
