<?php

namespace Aparlay\Core\Api\V1\Dto;

use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use MongoDB\BSON\ObjectId;

trait CreatorDTOTrait
{
    public array $creator = [];
    public ObjectId|null $created_by;
    public ObjectId|null $updated_by;

    public function setCreator(User|Authenticatable $user)
    {
        $this->creator    = [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ];
        $this->created_by = new ObjectId($user->_id);
        $this->updated_by = new ObjectId($user->_id);
    }
}
