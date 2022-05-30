<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\Alert as AlertBase;
use Aparlay\Payout\Api\V1\Models\UserPayout;
use Aparlay\Payout\Api\V1\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\Relation;

class Alert extends AlertBase
{
    public static function boot()
    {
        parent::boot();

        // to keep entities in database without namespace
        Relation::morphMap([
            'UserPayout' => UserPayout::class,
            'UserDocument' => UserDocument::class,
            'Wallet' => Wallet::class,
        ]);
    }
}
