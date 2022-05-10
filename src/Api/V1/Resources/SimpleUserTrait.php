<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\Cdn;
use Exception;
use Illuminate\Support\Facades\Cache;
use MongoDB\BSON\ObjectId;

trait SimpleUserTrait
{
    private string $cacheKeyPrefix = 'SimpleUserCast:';
    private array $simpleUser = [];

    /**
     * Create the simple user attribute.
     *
     * @param string[] $fields
     *
     * @throws Exception
     */
    public function createSimpleUser(
        array $userArray,
        array $fields = ['_id', 'username', 'avatar', 'is_followed', 'is_liked', 'is_verified']
    ): array {
        $userData = $this->getSimpleUser($userArray['_id']);
        if (empty($userData)) {
            $userData = $userArray;
            $userData['avatar'] = $userArray['avatar'] ?? Cdn::avatar('default.jpg');
        }

        if (! auth()->guest()) {
            $userData['is_followed'] = (bool) $this->is_followed;
            $userData['is_online'] = empty($userArray['is_followed']) ? $this->is_online : $this->is_online_for_followers;
            $userData['is_liked'] = (bool) $this->is_liked;
        }

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $userData[$field] ?? null;
        }

        return $output;
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
                'is_verified' => (bool) $user->is_verified,
            ];
        }

        return $data;
    }

    private function cacheSimpleUser($user): void
    {
        $cacheKey = $this->cacheKeyPrefix.$user['_id'];
        Cache::store('octane')->put($cacheKey, json_encode($user), 300);
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Exception
     */
    private function getSimpleUser(string|ObjectId $id): array
    {
        $cacheKey = $this->cacheKeyPrefix.$id;
        if (($simpleUser = Cache::store('octane')->get($cacheKey, false)) !== false) {
            return json_decode($simpleUser, true); // cache already exists
        }

        return $this->createSimpleUserById($id);
    }
}
