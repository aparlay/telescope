<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

/**
 * @property string email
 * @property string otp
 * @property string password
 * @property string old_password
 */
class ChangePasswordRequest extends FormRequest
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
            'email' => ['email:rfc,dns', 'max:255', 'required_without:old_password'],
            'otp' => ['required_without:old_password'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'old_password' => ['required_without:email', Password::min(8)->letters()->numbers()],
        ];
    }

    /**
     * This function is responsible to perform pre-validation
     */
    public function prepareForValidation()
    {
        /* Convert uppercase email charecter into lowercase */
        $this->username = $this->email ? Str::lower($this->email) : $this->phone_number;

        /** Resposible for match old password */
        if ($this->old_password && auth()->user()) {
            if (!Hash::check($this->old_password, auth()->user()->password_hash)) {
                throw ValidationException::withMessages([
                    'password' => ['Incorrect Old Password.'],
                ]);
            }
        }
    }
}
