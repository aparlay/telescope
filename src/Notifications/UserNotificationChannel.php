<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Api\V1\Dto\NotificationDto;
use Aparlay\Core\Api\V1\Services\NotificationService;
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
        $notificationDTO = NotificationDto::fromArray($notification->toArray($notifiable));

        $notificationService = app()->make(NotificationService::class);
        $notificationService->create($notificationDTO);
    }
}
