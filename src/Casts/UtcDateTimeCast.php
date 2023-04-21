<?php

namespace Aparlay\Core\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\UTCDateTime;

class UtcDateTimeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model  $model
     * @param string $key
     * @param mixed  $value
     * @param array  $attributes
     *
     * @return Carbon
     */
    public function get($model, $key, $value, $attributes)
    {
        // Convert UTCDateTime instances.
        if ($value instanceof UTCDateTime) {
            return Date::createFromTimestampMs($value->toDateTime()->format('Uv'));
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model  $model
     * @param string $key
     * @param array  $value
     * @param array  $attributes
     *
     * @return UTCDateTime
     */
    public function set($model, $key, $value, $attributes)
    {
        return new UTCDateTime($value);
    }
}
