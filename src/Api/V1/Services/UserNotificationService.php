<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserNotificationDto;
use Aparlay\Core\Api\V1\Models\UserDocument;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class UserNotificationService
{
    use HasUserTrait;

    /**
     * @param $filteredCategory
     * @return LengthAwarePaginator
     */
    public function index($filteredCategory = null): LengthAwarePaginator
    {
        $query = UserNotification::query()->with('entityObj')->user($this->getUser()->_id);

        if (! empty($filteredCategory)) {
            $query->category($filteredCategory);
        }

        $notifications = $query->latest('updated_at')->paginate();

        $notificationIds = collect($notifications->items())->pluck('_id')->toArray();
        $this->readAll($notificationIds);

        return $notifications;
    }

    /**
     * Responsible to create like for given media.
     *
     * @param  UserNotificationDto  $notificationDto
     * @return Model|UserNotification|null
     */
    public function create(UserNotificationDto $notificationDto): Model|UserNotification|null
    {
        $data = $notificationDto->except('user')->toArray();
        $data['user_id'] = new ObjectId($this->getUser()->_id);
        $data['entity._id'] = new ObjectId($data['entity_id']);
        $data['entity._type'] = $data['entity_type'];

        if (in_array($data['category'], [UserNotificationCategory::COMMENTS->value, UserNotificationCategory::LIKES->value])) {
            $model = UserNotification::query()
                ->entity($data['entity._id'], $data['entity._type'])
                ->user($this->getUser()->_id)
                ->category($data['category'])
                ->first();
        }

        if (empty($model)) {
            $model = UserNotification::create($data);
        } else {
            $model->update(['status' => UserNotificationStatus::NOT_VISITED->value]);
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
     * @param  array  $notifications
     */
    public function readAll(array $notificationIds): void
    {
        dispatch(function () use ($notificationIds) {
            UserNotification::query()
                ->whereIn('_id', $notificationIds)
                ->notVisited()
                ->update(['status' => UserNotificationStatus::VISITED->value]);
        });
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
