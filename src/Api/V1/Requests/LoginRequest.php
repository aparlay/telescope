<?php

namespace Aparlay\Core\Api\V1\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * @property string $otp
 * @property string $password
 * @property string $username
 */
class LoginRequest extends FormRequest
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
            'username' => 'required',
            'password' => 'required',
            'otp' => 'nullable',
        ];
    }

    /**
     * This function is responsible to perform pre-validation tasks like
     * Set username to lower case.
     *
     * @throws Exception
     */
    public function prepareForValidation()
    {
        $this->merge([
            'username' => Str::of($this->username)->trim()->lower()->toString(),
        ]);
    }
}
