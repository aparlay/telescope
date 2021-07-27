<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

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
            'email' => ['nullable','email','unique:users','max:255', 'required_without:phone_number'],
            'phone_number' => ['nullable','numeric','required_without:email'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'gender' => [Rule::in(array_keys(User::getGenders()))],
            'username' => ['nullable','unique:users'],
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

        /** Set the request parameters implemented above */
        $this->merge([
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'avatar' => $this->avatar,
            'gender' => $this->gender,
        ]);
    }
}
