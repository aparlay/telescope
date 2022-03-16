<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Api\V1\Dto\UserNotificationDto;
use Aparlay\Core\Api\V1\Services\UserNotificationService;
use Aparlay\Core\Events\UserNotificationEvent;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Payment\Models\Tip;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Notifications\Notification;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

final class UserNotificationChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     * @throws BindingResolutionException
     * @throws UnknownProperties
     */
    public function send(Tip|Media|Follow|User $notifiable, Notification $notification)
    {
        $notificationDTO = UserNotificationDto::fromArray($notification->toArray($notifiable));

        $notificationService = app()->make(UserNotificationService::class);
        $userNotification = $notificationService->create($notificationDTO);

        UserNotificationEvent::dispatch((string) $userNotification->_id, (string) $userNotification->user_id);
    }
}
