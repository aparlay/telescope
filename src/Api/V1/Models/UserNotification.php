<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification as UserNotificationBase;
use Illuminate\Database\Eloquent\Relations\Relation;

class UserNotification extends UserNotificationBase
{
    public static function boot()
    {
        parent::boot();

        // to keep entities in database without namespace
        Relation::morphMap([
            'Media' => Media::class,
            'User' => User::class,
            'Tip' => 'Aparlay\Payment\Models\Tip',
        ]);
    }
}
