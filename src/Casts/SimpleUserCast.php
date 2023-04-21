<?php

namespace Aparlay\Core\Casts;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\User;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Cache;
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
     * @param Model $model
     * @param mixed $value
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Exception
     *
     * @return array
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (!isset($value['_id'])) {
            return [];
        }

        $userArray = self::cacheByUserId($value['_id']);

        if (in_array('is_followed', $this->fields, true)) {
            $isFollowed               = false;
            if (!auth()->guest()) {
                $loggedInUserId = auth()->user()->_id;
                $isFollowed     = Follow::checkCreatorIsFollowedByUser(
                    (string) $userArray['_id'],
                    (string) $loggedInUserId
                );
            }

            $userArray['is_followed'] = $isFollowed;
        }

        return $userArray;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param mixed $value
     *
     * @return array[]
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (!isset($value['_id'])) {
            return;
        }

        $user = User::user($value['_id'])->first();

        return [
            $key => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
        ];
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function cacheByUserId(ObjectId|string $userId, bool $refresh = false): array
    {
        $userId   = $userId instanceof ObjectId ? (string) $userId : $userId;
        $cacheKey = 'SimpleUserCast:' . $userId;

        if ($refresh) {
            Cache::store('octane')->delete($cacheKey);
            Cache::store('redis')->delete($cacheKey);
        }

        if (($userArray = Cache::store('octane')->get($cacheKey, false)) !== false) {
            return is_string($userArray) ? json_decode($userArray, true) : $userArray; // cache already exists
        }

        if (empty($userArray = Cache::store('redis')->get($cacheKey, []))) {
            $user = User::user($userId)->first();
            if ($user !== null) {
                $userArray = [
                    '_id' => (string) $user->_id,
                    'username' => $user->username,
                    'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
                    'is_verified' => $user->is_verified,
                ];

                Cache::store('octane')->put($cacheKey, json_encode($userArray), config('app.cache.tenMinutes'));
                Cache::store('redis')->set($cacheKey, $userArray, config('app.cache.veryLongDuration'));
            }
        }

        if (Cache::store('octane')->get($cacheKey, false) === false) {
            Cache::store('octane')->put($cacheKey, json_encode($userArray), config('app.cache.tenMinutes'));
        }

        return $userArray;
    }
}
