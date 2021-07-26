<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'email' => ['nullable','email','unique:users','max:100', 'required_without:phone_number'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required'],
            'gender' => ['required','numeric', Rule::in(array_keys(User::getGenders()))],
            'username' => ['nullable','unique:users','min:6','max:20'],
            'phone_number' => ['nullable','numeric','required_without:email'],
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

        /** Set the request parameters implemented above */
        $this->merge([
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'avatar' => $this->avatar,
        ]);
    }
}
