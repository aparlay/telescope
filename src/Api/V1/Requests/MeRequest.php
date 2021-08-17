<?php

namespace Aparlay\Core\Api\V1\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\RequiredIf;

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
            'avatar' => ['image', 'mimes:png,jpg,jpeg,gif', 'max:10485760'],
            'username' => ['unique:users', 'min:2', 'max:255'],
            'device_id' => new RequiredIf($this->header('X-DEVICE-ID') == ''),
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'device_id.required' => 'Device Id cannot be blank.',
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

        /* Set the Default Values and required to be input parameters */
        $this->merge([
            'username' => trim($this->username),
        ]);
    }
}
