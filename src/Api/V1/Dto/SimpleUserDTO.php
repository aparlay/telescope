<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Casts\ObjectIdCast;
use Aparlay\Core\Models\User;
use MongoDB\BSON\ObjectId;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

#[
    DefaultCast(ObjectId::class, ObjectIdCast::class),
]
class SimpleUserDTO extends DataTransferObject
{
    public ObjectId $_id;
    public string $username;
    public string $avatar;

    /**
     * @throws UnknownProperties
     */
    public static function fromUserObj(User $user): self
    {
        return new self(
            [
                '_id' => $user->_id,
                'username' => $user->username,
                'avatar' => $user->avatar,
            ]
        );
    }
}
