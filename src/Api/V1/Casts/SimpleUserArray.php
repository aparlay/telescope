<?php

namespace Aparlay\Core\Api\V1\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class SimpleUserArray implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, string $key, $value, array $attributes): array
    {
        $value = $value ?? [];

        foreach ($value as $key => $item) {
            $value[$key] = $this->castString($item);
        }
        //$result = array_map([$this, 'castString'], $value);

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        $value = $value ?? [];

        return array_map([$this, 'castObjectId'], $value);
    }

    /**
     * @param  array  $item
     * @return array
     */
    protected function castObjectId(array $item): array
    {
        $item['_id'] = is_string($item['_id']) ? new ObjectId($item['_id']) : $item['_id'];

        return $item;
    }

    /**
     * @param array $item
     * @return array
     */
    protected function castString(array $item): array
    {
        $item['_id'] = $item['_id'] instanceof ObjectId ? (string) $item['_id'] : $item['_id'];

        return $item;
    }
}
