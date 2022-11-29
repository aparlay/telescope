<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserGender;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserType;
use Aparlay\Core\Models\Enums\UserVisibility;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use MongoDB\BSON\ObjectId;

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
            'username' => ['unique:users', 'min:2', 'max:30', 'alpha_dash'],
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

        if (! empty($this->referral_id) && ! ($this->referral_id instanceof ObjectId)) {
            if (($user = User::user($this->referral_id)->first()) !== null) {
                $this->referral_id = $user->_id;
            } elseif (($user = User::username($this->referral_id)->first()) !== null) {
                $this->referral_id = $user->_id;
            } elseif (($user = User::email($this->referral_id)->first()) !== null) {
                $this->referral_id = $user->_id;
            } elseif (($user = User::phoneNumber($this->referral_id)->first()) !== null) {
                $this->referral_id = $user->_id;
            }
        }

        $this->username = uniqid('', false);

        /* Set gender by default value */
        if (! is_int($this->gender)) {
            $genderValue = array_search($this->gender, User::getGenders());
            $this->gender = $genderValue !== false ? $genderValue : UserGender::MALE->value;
        }

        /* Set avatar based on Gender */
        if (empty($this->avatar)) {
            $femaleFilename = 'default_fm_'.random_int(1, 60).'.png';
            $maleFilename = 'default_m_'.random_int(1, 120).'.png';
            $filename = match ($this->gender) {
                UserGender::FEMALE->value => $femaleFilename,
                UserGender::MALE->value => $maleFilename,
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
            'status' => UserStatus::PENDING->value,
            'visibility' => UserVisibility::PUBLIC->value,
            'email_verified' => false,
            'phone_number_verified' => false,
            'type' => UserType::USER->value,
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
                'otp' => config('app.otp.enabled'),
                'show_adult_content' => 2,
                'filter_content_gender' => [
                    'female' => true,
                    'male' => false,
                    'transgender' => false,
                ],
                'notifications' => [
                    'unread_message_alerts' => false,
                    'new_followers' => false,
                    'news_and_updates' => false,
                    'tips' => false,
                    'new_subscribers' => false,
                ],
                'payment' => [
                    'allow_unverified_cc' => false,
                    'block_unverified_cc' => true,
                    'block_payments' => true,
                    'unverified_cc_spent_amount' => 0,
                ],
            ],
            'referral_id' => $this->referral_id,
            'features' => array_fill_keys(array_keys(User::getFeatures()), false),
            'last_online_at' => DT::utcNow(),
        ]);
    }
}
