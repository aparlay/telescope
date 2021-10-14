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
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return (string) $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (! empty($value)) {
            $value = new ObjectId($value);
        }

        return $value;
    }
}
