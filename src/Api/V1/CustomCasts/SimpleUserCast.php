<?php

namespace Aparlay\Core\Api\V1\CustomCasts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SimpleUserCast implements CastsAttributes
{

    /**
     * SimpleUserCast constructor.
     */
    public function __construct()
    {
    }

    public function get($model, string $key, $value, array $attributes)
    {
        $data = [];
        if (! empty($value)) {
            foreach ($value as $val) {
                $data[] = [
                    '_id' => (string) $val['_id'],
                    'username' => $val['username'],
                    'avatar' => $val['avatar'],
                ];
            }
        }
        return $data;
    }

    public function set($model, string $key, $value, array $attributes)
    {
    }
}
