<?php

namespace Aparlay\Core\Api\V1\Requests;

use Aparlay\Core\Models\Email;
use Illuminate\Http\Request;

/**
 * @property string to
 */
class EmailRequest extends Request
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
        return [];
    }

    /**
     * This function is responsible to prepare the default value of Email table columns.
     */
    public function prepareForValidation()
    {
        $this->user = (object) $this->user;

        /* Set the Default Values and required to be input parameters */
        $this->merge([
                'to' => $this->to,
                'user' => [
                    '_id' => $this->user->_id,
                    'username' => $this->user->username,
                    'avatar' => $this->user->avatar,
                ],
                'status' => ($this->status) ?: Email::STATUS_QUEUED,
                'type' => ($this->type) ?: Email::TYPE_OTP,
        ]);
    }
}
