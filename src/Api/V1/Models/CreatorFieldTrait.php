<?php

namespace Aparlay\Core\Api\V1\Models;

use MongoDB\BSON\ObjectId;

trait CreatorFieldTrait
{
    /**
     * Set the mediaLike's creator.
     *
     * @param mixed $creator
     *
     * @return void
     */
    public function setCreatorAttribute($creator)
    {
        $creator                     = User::user($creator['_id'])->first();

        $this->attributes['creator'] = [
            '_id' => new ObjectId($creator->_id),
            'username' => $creator->username,
            'avatar' => $creator->avatar,
        ];
    }
}
