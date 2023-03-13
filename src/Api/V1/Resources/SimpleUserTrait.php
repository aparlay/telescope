<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Exception;
use Illuminate\Support\Facades\Cache;
use Laravel\Octane\Exceptions\TaskException;
use Laravel\Octane\Exceptions\TaskTimeoutException;
use Laravel\Octane\Facades\Octane;
use MongoDB\BSON\ObjectId;
use Swoole\Coroutine\WaitGroup;

trait SimpleUserTrait
{
    private string $cacheKeyPrefix = 'SimpleUserCast:';
    private array $simpleUser = [];

    /**
     * @param  array  $rawUsers
     * @param  array  $fields
     *
     * @return array
     * @throws Exception
     */
    public function createBatchSimpleUser(
        array $rawUsers,
        array $fields = ['_id', 'username', 'avatar', 'is_followed', 'is_liked', 'is_verified']
    ): array {
        $users = [];
        foreach ($rawUsers as $user) {
            $users[] = $this->createSimpleUser($user, $fields);
        }

        return $users;
    }

    /**
     * Create the simple user attribute.
     *
     * @param  string[]  $fields
     *
     * @throws Exception
     */
    public function createSimpleUser(
        array $userArray,
        array $fields = ['_id', 'username', 'avatar', 'is_followed', 'is_liked', 'is_verified']
    ): array {
        if (! isset($userArray['_id'])) {
            return [];
        }

        $userData = $this->getSimpleUser($userArray['_id']);
        if (empty($userData)) {
            $userData = $userArray;
            $userData['avatar'] = $userArray['avatar'] ?? Cdn::avatar('default.jpg');
        }

        if (! auth()->guest()) {
            $userData['is_followed'] = $this->is_followed;
            $userData['is_online'] = empty($userArray['is_followed']) ? $this->is_online : $this->is_online_for_followers;
            $userData['is_liked'] = $this->is_liked;
        }

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $userData[$field] ?? null;
        }

        return $output;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Exception
     */
    private function getSimpleUser(string|ObjectId $id): array
    {
        $cacheKey = $this->cacheKeyPrefix.$id;
        if (! config('app.is_testing') && ($simpleUser = Cache::store('octane')->get($cacheKey, false)) !== false) {
            return json_decode($simpleUser, true); // cache already exists
        }

        $user = $this->createSimpleUserById($id);
        $this->cacheSimpleUser($user);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function createSimpleUserById(string|ObjectId $id): array
    {
        $data = [];
        if (($user = User::user($id)->first()) !== null) {
            $data = [
                '_id' => (string) $user->_id,
                'username' => $user->username,
                'avatar' => $user->avatar ?? Cdn::avatar('default.jpg'),
                'is_verified' => $user->is_verified,
            ];
        }

        return $data;
    }

    private function cacheSimpleUser($user): void
    {
        if (! config('app.is_testing') && ! empty($user)) {
            $cacheKey = $this->cacheKeyPrefix.$user['_id'];
            Cache::store('octane')->put($cacheKey, json_encode($user), config('app.cache.tenMinutes'));
        }
    }
}
