<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Api\V1\Dto\UserNotificationDto;
use Aparlay\Core\Api\V1\Services\UserNotificationService;
use Aparlay\Core\Events\UserNotificationEvent;
use Aparlay\Core\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
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
    public function send(mixed $notifiable, Notification $notification)
    {
        $user = User::user($notification->user_id)->first();
        Log::debug('UserNotificationChannel: send');

        $notificationDTO = UserNotificationDto::fromArray($notification->toArray($notifiable));

        if ($user->shouldNotify($notificationDTO->category)) {
            Log::debug('UserNotificationChannel: should notify');
            $notificationService = app()->make(UserNotificationService::class);
            $notificationService->setUser($user);
            $userNotification = $notificationService->create($notificationDTO);

            UserNotificationEvent::dispatch(
                (string) $userNotification->_id,
                (string) $userNotification->user_id,
                $notification->message ?? '',
                $notification->payload ?? [],
                $notification->eventType ?? '',
            );
        }
    }
}
