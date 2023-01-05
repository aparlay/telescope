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
            'user_id' => ['required'],
            'email' => [
                'required',
                'email:rfc,spoof,dns',
                Rule::unique('users', 'email')->ignore($this->user_id, '_id'),
                'max:255',
            ],
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($this->user_id, '_id'),
                'min:3',
                'max:30',
                'alpha_dash',
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
            'user_id' => new ObjectId($this->user_id),
            'referral_id' => $this->referral_id ? new ObjectId($this->referral_id) : null,
        ]);
    }
}
