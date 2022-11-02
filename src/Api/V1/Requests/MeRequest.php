<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Models\Enums\UserInterestedIn;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @property string email
 * @property string phone_number
 * @property string username
 * @property string password
 * @property string gender
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
        return [
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'max:10485760'],
            'username' => ['unique:users', 'min:2', 'max:255', 'alpha_dash'],
            'country_alpha2' => [Rule::in(array_keys(Country::getAlpha2AndNames()))],
            'payout_country_alpha2' => [Rule::in(array_keys(Country::getAlpha2AndNames()))],
            'interested_in' => ['nullable', 'array'],
            'interested_in.*' => [Rule::in(UserInterestedIn::getAllValues())],
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
        if (!is_array($this->interested_in)) {
            throw ValidationException::withMessages([
                'interested_in' => 'interested_in must be an array of interested choices.',
            ]);
        }
        $this->merge([
            'interested_in' => array_map('intval', $this->interested_in),
        ]);
    }
}
