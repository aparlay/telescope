<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *      title="Change Password request",
 *      description="Change password request body data",
 *      type="object",
 *      required={"name"}
 * )
 *
 * @property string email
 * @property string old_password
 * @property string otp
 * @property string password
 */
class ChangePasswordRequest extends FormRequest
{
    /**
     * @OA\Property(property="email", type="string", title="Email", description="Email of the user who want to the change the password", example="andrey@gmail.com")
     * @OA\Property(property="phone_number", type="string", title="Phone Number", description="Phone number of the user who want to the change the password", example="+1482735414234")
     * @OA\Property(property="otp", type="string", title="Otp", description="One time password sent to the user provided identity", example="4234")
     * @OA\Property(property="password", type="string", title="Otp", description="New password for the user alpha-numeric and underscore at least 8 chars", example="A_Very5ecure_password")
     * @OA\Property(property="old_password", type="string", title="Otp", description="if user is going to change password it must provide the old password (current password)", example="A_Very5ecure_password_old")
     */

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
            'password' => ['required', Password::min(8)->letters()->numbers(), 'different:old_password'],
            'old_password' => ['required_without:email', Password::min(8)->letters()->numbers()],
        ];
    }

    /**
     * This function is responsible to perform pre-validation.
     */
    public function prepareForValidation()
    {
        /* Convert uppercase email charecter into lowercase */
        $this->email        = Str::of($this->email)->trim()->lower()->toString();
        $this->phone_number = Str::of($this->phone_number)->trim()->lower()->toString();
        $this->username     = !empty($this->email) ? $this->email : $this->phone_number;

        /* Responsible to match old password */
        if ($this->old_password && auth()->user()) {
            if (!Hash::check($this->old_password, auth()->user()->password_hash)) {
                throw ValidationException::withMessages([
                    'password' => ['Incorrect Old Password.'],
                ]);
            }
        }
    }
}
