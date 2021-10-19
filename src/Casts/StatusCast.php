<?php

namespace Aparlay\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Jenssegers\Mongodb\Eloquent\Model;

class StatusCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $model::getStatuses()[$value];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \MongoDB\BSON\ObjectId
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return $value;
        }

        return $value instanceof \MongoDB\BSON\ObjectId ? $value : new \MongoDB\BSON\ObjectId($value);
    }
}
