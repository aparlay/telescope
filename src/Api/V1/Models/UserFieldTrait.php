<?php

namespace Aparlay\Core\Api\V1\Models;

use MongoDB\BSON\ObjectId;

trait UserFieldTrait
{
    /**
     * Set the follow user attribute.
     *
     * @return void
     */
    public function setUserAttribute($user)
    {
        $user = User::user($user['_id'])->first();
        
        $this->attributes['user'] = [
            '_id' => $user->_id,
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];
    }
}
