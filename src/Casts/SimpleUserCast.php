<?php

namespace Aparlay\Core\Casts;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class SimpleUserCast implements CastsAttributes
{
    private array $fields;

    public function __construct(...$fields)
    {
        $this->fields = array_map('trim', $fields);
    }

    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     * @throws \Exception
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $user = User::user($value['_id'])->first();
        $userArray = [
            '_id' => (string) $user->_id,
            'username' => $user->username,
            'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
        ];

        if (in_array('is_followed', $this->fields, true)) {
            $userArray['is_followed'] = $user->is_followed;
        }

        if (in_array('is_liked', $this->fields, true)) {
            $userArray['is_liked'] = ($model instanceof Media) ? $model->is_liked : false;
        }

        return $userArray;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array[]
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $user = User::user($value['_id'])->first();

        return [$key => [
            '_id' => new ObjectId($user->_id),
            'username' => $user->username,
            'avatar' => $user->avatar,
        ]];
    }
}
