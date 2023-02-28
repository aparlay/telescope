<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserSettingShowAdultContent;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\Enums\UserVisibility;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @property string $username
 * @property int    $visibility
 * @property string $country_alpha2
 * @property string $payout_country_alpha2
 * @property array  $setting
 */
class MeRequest extends FormRequest
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
        $user = auth()->user();

        return [
            'avatar' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,gif',
                'max:10485760',
            ],
            'bio' => ['nullable', 'string', 'min:3', 'max:200'],
            'promo_link' => ['nullable', 'url'],
            'username' => [
                'nullable',
                'min:3',
                'max:30',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->_id, '_id'),
            ],
            'payout_country_alpha2' => [
                'nullable',
                Rule::in(array_keys(Country::getAlpha2AndNames())),
            ],
            'country_alpha2' => [
                'nullable',
                Rule::in(array_keys(Country::getAlpha2AndNames())),
            ],
            'visibility' => [
                'nullable',
                Rule::in([
                    UserVisibility::PRIVATE->value,
                    UserVisibility::PUBLIC->value,
                ]),
            ],
            'setting.otp' => ['nullable', 'bool'],
            'setting.show_adult_content' => ['nullable', 'integer'],
            'setting.notifications.*' => ['nullable', 'bool'],
            'setting.filter_content_gender.*' => ['nullable', 'bool'],
            'setting.fcm_tokens.*' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.alpha_dash' => 'Username is invalid',
        ];
    }

    /**
     * This function is responsible to perform pre-validation tasks.
     *
     * @throws Exception
     */
    public function prepareForValidation()
    {
        if (is_array($this->avatar)) {
            throw ValidationException::withMessages([
                'avatar' => 'You can upload only one image file.',
            ]);
        }

        if (! auth()->guest() && auth()->user()->is_invisible && isset($this->visibility)) {
            throw ValidationException::withMessages([
                'visibility' => 'Your account is invisible by administrator, you cannot change it to public/private.',
            ]);
        }

        if ($this->has('username')) {
            $this->merge(['username' => trim($this->username)]);
        }

        if (! auth()->guest()
            && auth()->user()->status === UserStatus::ACTIVE->value
            && $this->has('username')
        ) {
            throw ValidationException::withMessages([
                'username' => 'You cannot change your username any more.',
            ]);
        }
        if (! auth()->guest()
            && ! empty(auth()->user()->payout_country_alpha2)
            && $this->has('payout_country_alpha2')
        ) {
            throw ValidationException::withMessages([
                'username' => 'You cannot change your payout country any more.',
            ]);
        }

        $user = auth()->user();
        /* Set the Default Values and required to be input parameters */
        $this->merge([
            'setting' => [
                'otp' => $this->setting['otp'] ?? $user->setting['otp'] ?? false,
                'show_adult_content' => $this->setting['show_adult_content'] ??
                        $user->setting['show_adult_content'] ?? UserSettingShowAdultContent::ASK->value,
                'filter_content_gender' => [
                    'female' => $this->setting['filter_content_gender']['female'] ??
                            $user->setting['filter_content_gender']['female'] ?? true,
                    'male' => $this->setting['filter_content_gender']['male'] ??
                            $user->setting['filter_content_gender']['male'] ?? false,
                    'transgender' => $this->setting['filter_content_gender']['transgender'] ??
                            $user->setting['filter_content_gender']['transgender'] ?? false,
                ],
                'notifications' => [
                    'unread_message_alerts' => $this->setting['notifications']['unread_message_alerts'] ??
                            $user->setting['notifications']['unread_message_alerts'] ?? true,
                    'new_followers' => $this->setting['notifications']['new_followers'] ??
                            $user->setting['notifications']['new_followers'] ?? true,
                    'news_and_updates' => $this->setting['notifications']['news_and_updates'] ??
                            $user->setting['notifications']['news_and_updates'] ?? true,
                    'new_subscribers' => $this->setting['notifications']['new_subscribers'] ??
                            $user->setting['notifications']['new_subscribers'] ?? true,
                    'tips' => $this->setting['notifications']['tips'] ??
                            $user->setting['notifications']['tips'] ?? true,
                    'likes' => $this->setting['notifications']['likes'] ??
                            $user->setting['notifications']['likes'] ?? true,
                    'comments' => $this->setting['notifications']['comments'] ??
                            $user->setting['notifications']['comments'] ?? true,
                ],
                'payment' => [
                    'allow_unverified_cc' => $user->setting['payment']['allow_unverified_cc'] ?? false,
                    'block_unverified_cc' => $user->setting['payment']['block_unverified_cc'] ?? true,
                    'block_cc_payments' => $user->setting['payment']['block_cc_payments'] ?? true,
                    'unverified_cc_spent_amount' => (int) ($user->setting['payment']['unverified_cc_spent_amount'] ??
                        0),
                ],
                'payout' => [
                    'ban_payout' => $user->setting['payout']['ban_payout'] ?? false,
                    'auto_ban_payout' => $user->setting['payout']['auto_ban_payout'] ?? false,
                ],
                'fcm_tokens' => array_merge(
                    array_values($user->setting['fcm_tokens'] ?? []),
                    array_values($this->setting['fcm_tokens'] ?? [])
                )
            ],
        ]);
    }
}
