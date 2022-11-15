<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Dto\UserNotificationDto;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Models\MediaLike;
use Aparlay\Core\Api\V1\Models\UserNotification;
use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Aparlay\Core\Events\UserNotificationUnreadStatusUpdatedEvent;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
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
        $userId = $this->getUser()->_id;
        $query = UserNotification::query()->user($userId);
        /*
         ->with(['entityObj' => function (MorphTo $morphTo) {
            $morphTo->morphWith([
                'Media',
                'User',
                'Tip',
            ]);
        }])
        */

        if (! empty($filteredCategory)) {
            $query->category($filteredCategory);
        }

        $notifications = $query->latest('created_at')->paginate();

        $notificationIds = collect($notifications->items())->pluck('_id')->toArray();
        $this->readAll($userId, $notificationIds);

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
            $userNotification = UserNotification::query()
                ->entity($data['entity._id'], $data['entity._type'])
                ->user($this->getUser()->_id)
                ->category($data['category'])
                ->first();

            $media = Media::media($data['entity._id'])->first();
            if ($data['category'] === UserNotificationCategory::LIKES->value) {
                $mediaLikes = MediaLike::query()->with('creatorObj')->media($data['entity._id'])->recent()->limit(2)->get();
                $message = match (true) {
                    ($media->like_count > 2 && isset($mediaLikes[0]->creatorObj->username, $mediaLikes[1]->creatorObj->username)) => __(':username1, :username2 and :count others liked your video.', ['username1' => $mediaLikes[0]->creatorObj->username, 'username2' => $mediaLikes[1]->creatorObj->username, 'count' => $media->like_count - 2]),
                    ($media->like_count === 2 && isset($mediaLikes[0]->creatorObj->username, $mediaLikes[1]->creatorObj->username)) => __(':username1 and :username2 liked your video.', ['username1' => $mediaLikes[1]->creatorObj->username, 'username2' => $mediaLikes[1]->creatorObj->username]),
                    default => __(':username liked your video.', ['username' => $mediaLikes[0]->creatorObj->username])
                };
            } else {
                $mediaComments = MediaComment::query()->with('creatorObj')->media($data['entity._id'])->recent()->limit(2)->get();
                $message = match (true) {
                    ($media->comment_count > 2 && isset($mediaComments[0]->creatorObj->username, $mediaComments[1]->creatorObj->username)) => __(':username1, :username2 and :count others commented on your video.', ['username1' => $mediaComments[0]->creatorObj->username, 'username2' => $mediaComments[1]->creatorObj->username, 'count' => $media->comment_count - 2]),
                    ($media->comment_count === 2 && isset($mediaComments[0]->creatorObj->username, $mediaComments[1]->creatorObj->username)) => __(':username1 and :username2 commented on your video.', ['username1' => $mediaComments[0]->creatorObj->username, 'username2' => $mediaComments[1]->creatorObj->username]),
                    default => __(':username liked your video.', ['username' => $mediaComments[0]->creatorObj->username])
                };
            }
        }

        if (empty($userNotification)) {
            $userNotification = UserNotification::create($data);
        } else {
            $userNotification->update([
                'status' => UserNotificationStatus::NOT_VISITED->value,
                'updated_at' => DT::utcNow(),
                'message' => $message ?? $userNotification->message,
                'payload' => $notificationDto->payload ?? $userNotification->payload,
            ]);
        }

        $this->getUser()->increaseStatCounter('notifications');

        return $userNotification;
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
            $userId = $this->getUser()->_id;
            $hasUnreadNotifications = $this->getUser()->has_unread_notification;
            $notification->update(['status' => UserNotificationStatus::VISITED->value]);

            dispatch(function () use ($userId) {
                $unreadNotificationCount = UserNotification::query()->user($userId)->notVisited()->count();
                $this->getUser()->setStatCounter('notifications', $unreadNotificationCount);
            });

            UserNotificationUnreadStatusUpdatedEvent::dispatch(
                $userId,
                $hasUnreadNotifications
            );
        }

        return $notification;
    }

    /**
     * Responsible to unlike the given media.
     *
     * @param  ObjectId|string  $userId
     * @param  array  $notificationIds
     */
    public function readAll(ObjectId|string $userId, array $notificationIds): void
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);
        $hasUnreadNotifications = $this->getUser()->has_unread_notification;

        UserNotification::query()
            ->user($userId)
            ->notVisited()
            ->whereInIds('_id', $notificationIds)
            ->update(['status' => UserNotificationStatus::VISITED->value]);

        UserNotificationUnreadStatusUpdatedEvent::dispatch(
            $userId,
            $hasUnreadNotifications,
            false
        );
    }

    /**
     * Responsible to unlike the given media.
     *
     * @param  UserNotification  $notification
     * @return UserNotification
     */
    public function unread(UserNotification $notification): UserNotification
    {
        $hasUnreadNotifications = $this->getUser()->has_unread_notification;

        if ($notification->status === UserNotificationStatus::VISITED->value) {
            $notification->update(['status' => UserNotificationStatus::NOT_VISITED->value]);
        }

        UserNotificationUnreadStatusUpdatedEvent::dispatch(
            $this->getUser()->_id,
            $hasUnreadNotifications,
            true
        );

        return $notification;
    }
}
