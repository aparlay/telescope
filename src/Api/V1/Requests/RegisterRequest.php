<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property string email
 * @property string phone_number
 * @property string username
 * @property string password
 * @property string gender
 */
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
            'email' => ['required', 'email:rfc,dns', 'unique:users', 'max:255'],
            'phone_number' => ['nullable', 'numeric', 'digits:10', 'unique:users'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'gender' => [Rule::in(array_keys(User::getGenders()))],
            'username' => ['nullable', 'max:255'],
        ];
    }

    /**
     * This function is responsible to perform pre-validation tasks like
     * Set the email or phone, based on username
     * Set the avatar, based on gender.
     *
     * @throws Exception
     */
    public function prepareForValidation()
    {
        /* Set email or phone basd on the usernmae format */
        if (str_contains($this->username, '@')) {
            $this->email = $this->username;
        }

        if ((int) $this->username > 100000) {
            $this->phone_number = $this->username;
        }

        $this->username = uniqid('', false);

        /* Set gender by default value */
        $this->gender = isset($this->gender) ? (int) $this->gender : User::GENDER_MALE;

        /* Set avatar based on Gender */
        if (empty($this->avatar)) {
            $femaleFilename = 'default_fm_'.random_int(1, 60).'.png';
            $maleFilename = 'default_m_'.random_int(1, 120).'.png';
            $filename = match ($this->gender) {
                User::GENDER_FEMALE => $femaleFilename,
                User::GENDER_MALE => $maleFilename,
                default => (random_int(0, 1) ? $maleFilename : $femaleFilename),
            };
            $this->avatar = Cdn::avatar($filename);
        }

        /* Set the Default Values and required to be input parameters */
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
            'type' => User::TYPE_USER,
            'full_name' => null,
            'promo_link' => null,
            'followers' => [],
            'followings' => [],
            'likes' => [],
            'blocks' => [],
            'followed_hashtags' => [],
            'medias' => [],
            'count_fields_updated_at' => [],
            'setting' => [
                'otp' => false,
                'notifications' => [
                    'unread_message_alerts' => false,
                    'new_followers' => false,
                    'news_and_updates' => false,
                    'tips' => false,
                    'new_subscribers' => false,
                ],
            ],
            'features' => array_fill_keys(array_keys(User::getFeatures()), false),
        ]);
    }
}
