<?php

namespace Aparlay\Core\Api\V1\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class SimpleUser implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array|null
     */
    public function get($model, string $key, $value, array $attributes): ?array
    {
        $value['_id'] = $value['_id'] instanceof ObjectId ? (string) $value['_id'] : $value['_id'];

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return array|null
     */
    public function set($model, string $key, $value, array $attributes): ?array
    {
        $value['_id'] = is_string($value['_id']) ? new ObjectId($value['_id']) : $value['_id'];

        return $value;
    }
}
