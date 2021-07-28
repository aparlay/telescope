<?php

namespace Aparlay\Core\Api\V1\Models;

use MongoDB\BSON\ObjectId;

trait CreatorFieldTrait
{

    /**
     * Get the creator simple user object
     *
     * @return string $value
     */
    public function getCreatorAttribute($creator)
    {
        $creator['_id'] = (string) $creator['_id'];

        if (auth()->guest()) {
            $creator['is_followed'] = false;
            $creator['is_liked'] = false;

            return $creator;
        }

        $user = auth()->user();
        $creator['is_followed'] = isset($this->creator['_id'], $user->following[(string) $this->creator['_id']]);

        return $creator;
    }

    /**
     * Set the mediaLike's creator.
     *
     * @return void
     */
    public function setCreatorAttribute($creator)
    {
        $creator = User::user($creator['_id'])->first();

        $this->attributes['creator'] = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];
    }
}
