<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'email' => ['required','email','unique:users', 'max:255'],
            'phone_number' => ['nullable','numeric','digits:10','unique:users'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'gender' => [Rule::in(array_keys(User::getGenders()))],
            'username' => ['nullable','max:255'],
        ];
    }

    /**
     * This function is responsible to perform pre-validation tasks like
     * Set the email or phone, based on username
     * Set the avatar, based on gender
     */
    public function prepareForValidation()
    { 
        /** Set email or phone basd on the usernmae format */
        if (strpos($this->username, '@') !== false) {
            $this->email = $this->username;
        }
        if ((int)$this->username > 100000) {
            $this->phone_number = $this->username;
        }
        $this->username = uniqid('', false);

        /** Set gender by default value */
        if(empty($this->gender)){
            $this->gender = User::GENDER_MALE;
        }

        /** Set avatar based on Gender */
        if (empty($this->avatar)) {
            switch ($this->gender) {
                case User::GENDER_FEMALE:
                    $filename = 'default_fm_' . random_int(1, 60) . '.png';

                    break;
                case User::GENDER_MALE:
                    $filename = 'default_m_' . random_int(1, 120)  . '.png';

                    break;
                default:
                    $filename = (((bool)random_int(0, 1)) ? 'default_m_' . random_int(1, 120) : 'default_fm_' . random_int(1, 60)) . '.png';
            }
            
            $this->avatar = Cdn::avatar($filename);
        }

        /** Set the Default Values and required input parameters */
        $this->merge([
            'username' => trim($this->username),
            'email' => strtolower(trim($this->email)),
            'phone_number' => $this->phone_number,
            'avatar' => $this->avatar,
            'gender' => $this->gender,
            'password_hash' => Hash::make($this->password),
            'status' => User::STATUS_PENDING,
            'visibility' => User::VISIBILITY_PUBLIC,
            'interested_in' => User::INTERESTED_IN_FEMALE,
            'email_verified' => false,
            'phone_number_verified' => false,
            'type' => User::TYPE_USER
        ]);
    }
}
