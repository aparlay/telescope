<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\NotificationDto;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class NotificationService
{
    use HasUserTrait;

    /**
     * Responsible to create like for given media.
     *
     * @param  NotificationDto  $notificationDto
     * @return Model|UserNotification|null
     */
    public function create(NotificationDto $notificationDto): Model|UserNotification|null
    {
        $data = $notificationDto->except('user')->toArray();
        $data['user_id'] = new ObjectId($this->getUser()->_id);
        try {
            $model = UserNotification::create($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            $model = null;
        }

        return $model;
    }

    /**
     * Responsible to unlike the given media.
     *
     * @param  UserNotification  $notification
     * @return UserNotification
     */
    public function read(UserNotification $notification): UserNotification
    {
        if ($notification->status === UserNotificationStatus::NOT_VISITED->value) {
            $notification->update(['status' => UserNotificationStatus::VISITED->value]);
        }

        return $notification;
    }

    /**
     * Responsible to unlike the given media.
     *
     * @param  UserNotification  $notification
     * @return UserNotification
     */
    public function unread(UserNotification $notification): UserNotification
    {
        if ($notification->status === UserNotificationStatus::VISITED->value) {
            $notification->update(['status' => UserNotificationStatus::NOT_VISITED->value]);
        }

        return $notification;
    }
}
