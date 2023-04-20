<?php

namespace Aparlay\Core\Observers;

use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use MongoDB\BSON\ObjectId;

class UserNotificationObserver extends BaseModelObserver
{
    /**
     * Handle the UserNotification "creating" event.
     *
     * @param UserNotification $model
     */
    public function creating($model): void
    {
        $user        = User::user($model->user['_id'])->first();

        $model->user = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];

        parent::creating($model);
    }
}
