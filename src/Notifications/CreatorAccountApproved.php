<?php

namespace Aparlay\Core\Notifications;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;

class CreatorAccountApproved extends Notification
{
    use Queueable;
    use UserNotificationArray;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User|Authenticatable $user, string $message)
    {
        $this->entity_type = User::class;
        $this->entity_id = $user->_id;
        $this->user_id = $user->_id;
        $this->category = UserNotificationCategory::COMMENTS->value;
        $this->status = UserNotificationStatus::NOT_VISITED->value;
        $this->message = $message;
    }
}
