<?php

namespace Aparlay\Core\Api\V1\Dto;

use MongoDB\BSON\ObjectId;
use Spatie\DataTransferObject\Caster;

class ObjectIdCaster implements Caster
{
    public function cast(mixed $value): ObjectId
    {
        return $value instanceof ObjectId ? $value : new ObjectId($value);
    }
}
