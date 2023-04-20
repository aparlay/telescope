<?php

namespace Aparlay\Core\Api\V1\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
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
            'user_notification_ids' => ['nullable'],
        ];
    }

    /**
     * This function is responsible to perform pre-validation.
     */
    public function prepareForValidation()
    {
        if (!is_array($this->user_notification_ids)) {
            throw ValidationException::withMessages([
                'user_notification_ids' => 'The user notification ids field is required.',
            ]);
        }

        $userNotificationIds = [];
        foreach ($this->user_notification_ids as $index => $notificationId) {
            if (!empty($notificationId) && strlen($notificationId) === 24 && strspn($notificationId, '0123456789ABCDEFabcdef') === 24) {
                $userNotificationIds[$index] = new ObjectId($notificationId);
            } else {
                unset($userNotificationIds[$index]);
            }
        }
        $this->merge([
            'user_notification_ids' => $userNotificationIds,
        ]);
    }
}
