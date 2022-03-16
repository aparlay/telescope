<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\User;
use Aparlay\Payment\Models\Tip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;

class TipSentNotification extends Notification
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User|Authenticatable $user, Tip $tip, string $message)
    {
        $this->usernotifiable_type = Tip::class;
        $this->usernotifiable_id = $tip->_id;
        $this->usernotifiable = $tip;
        $this->user_id = $user->_id;
        $this->category = UserNotificationCategory::TIPS->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'entity_type' => $this->usernotifiable_type,
            'entity_id' => $this->usernotifiable_id,
            'entity' => $this->usernotifiable,
            'category' => $this->category,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'message' => $this->message,
        ];
    }
}
