<?php

namespace Aparlay\Core\Admin\Models;

use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property string $endpoint
 * @property string|null $public_key
 * @property string|null $auth_token
 * @property string|null $content_encoding
 * @property \Illuminate\Database\Eloquent\Model $subscribable
 */
class PushSubscription extends \Aparlay\Core\Models\PushSubscription
{
    public static function boot()
    {
        parent::boot();

        // to keep entities in database without namespace
        Relation::morphMap([
            'User' => User::class,
        ]);
    }
}
