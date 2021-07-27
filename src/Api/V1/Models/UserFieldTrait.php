<?php

namespace Aparlay\Core\Api\V1\Models;

use MongoDB\BSON\ObjectId;

trait UserFieldTrait
{

    /**
     * Get the follow simple user object
     *
     * @return array
     */
    public function getUserAttribute($userObj)
    {
        $userObj['_id'] = (string) $userObj['_id'];

        if (auth()->guest()) {
            $userObj['is_followed'] = false;

            return $userObj;
        }

        $user = auth()->user();
        $userObj['is_followed'] = isset($this->creator['_id'], $user->following[(string) $this->creator['_id']]);

        return $userObj;
    }

    /**
     * Set the follow's creator.
     *
     * @return void
     */
    public function setUserAttribute($user)
    {
        $user = User::user($user['_id'])->first();

        $this->attributes['user'] = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];
    }
}
