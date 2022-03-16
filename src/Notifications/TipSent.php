<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\User;
use Aparlay\Payment\Models\Tip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;

class TipSent extends Notification
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
        $this->entity_type = Tip::class;
        $this->entity_id = $tip->_id;
        $this->user_id = $user->_id;
        $this->category = UserNotificationCategory::TIPS->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
    }
}
