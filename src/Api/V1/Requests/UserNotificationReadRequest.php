<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;

/**
 * @property array $user_notification_ids
 */
class UserNotificationReadRequest extends FormRequest
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
            'user_notification_ids' => ['required'],
        ];
    }

    /**
     * This function is responsible to perform pre-validation.
     */
    public function prepareForValidation()
    {
        foreach ($this->user_notification_ids as $index => $notificationId) {
            if (!empty($notificationId) && strlen($notificationId) === 24 && strspn($notificationId,'0123456789ABCDEFabcdef') === 24) {
                $this->user_notification_ids[$index] = new ObjectId($notificationId);
            } else {
                unset($this->user_notification_ids[$index]);
            }
        }
    }
}
