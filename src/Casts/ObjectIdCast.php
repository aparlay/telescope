<?php

namespace Aparlay\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class ObjectIdCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param mixed $value
     *
     * @return string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return ($value instanceof ObjectId || empty($value)) ? $value : new ObjectId($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param mixed $value
     *
     * @return ObjectId
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return (is_string($value) && !empty($value)) ? new ObjectId($value) : $value;
    }
}
