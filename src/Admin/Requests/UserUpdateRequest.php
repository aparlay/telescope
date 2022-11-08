<?php

namespace Aparlay\Core\Admin\Requests;

use Aparlay\Core\Admin\Models\User;
use Aparlay\Core\Helpers\Country;
use Aparlay\Core\Models\Enums\UserVerificationStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Maklad\Permission\Models\Role;
use MongoDB\BSON\ObjectId;

class UserUpdateRequest extends FormRequest
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
            'email' => [
                'required',
                'email:rfc,spoof,dns',
                Rule::unique('users', 'email')->ignore($this->user->_id, '_id'),
                'max:255',
            ],
            'username' => [
                'nullable',
                Rule::unique('users', 'username')->ignore($this->user->_id, '_id'),
                'max:255',
            ],
            'verification_status' => [
                'nullable', Rule::in(UserVerificationStatus::getAllValues()),
            ],
            'payout_country_alpha2' => ['nullable', Rule::in(array_keys(Country::getAlpha2AndNames()))],
            'phone_number' => ['nullable', 'numeric', 'digits:10', 'unique:users'],
            'country_alpha2' => ['nullable', Rule::in(array_keys(Country::getAlpha2AndNames()))],
            'gender' => [Rule::in(array_keys(User::getGenders()))],
            'type' => [Rule::in(array_keys(User::getTypes())), 'integer'],
            'status' => [Rule::in(array_keys(User::getStatuses()))],
            'interested_in' => [Rule::in(array_keys(User::getInterestedIns()))],
            'visibility' => [Rule::in(array_keys(User::getVisibilities()))],
            'role' => ['nullable', Rule::in(Role::where('guard_name', 'admin')->pluck('name'))],
            'email_verified' => ['nullable', 'boolean'],
            'features.tips' => ['nullable', 'boolean'],
            'features.demo' => ['nullable', 'boolean'],
            'bio' => ['nullable', 'string'],
            'promo_link' => ['nullable', 'url'],
            'referral_id' => ['nullable', Rule::exists((new User())->getCollection(), '_id')],
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors(); // Here is your array of errors

        throw new HttpResponseException(
            redirect()->back()->withErrors($errors)
        );
    }

    public function prepareForValidation()
    {
        $this->merge([
            'referral_id' => $this->referral_id ? new ObjectId($this->referral_id) : null,
        ]);
    }
}
