<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Helpers\Cdn;
use Exception;

trait SimpleUserTrait
{
    /**
     * Create the simple user attribute.
     *
     * @param string[] $fields
     *
     * @throws Exception
     */
    public function createSimpleUser(
        array $userArray,
        array $fields = ['_id', 'username', 'avatar', 'is_followed', 'is_liked']
    ): array {
        $user = auth()->user();
        $userArray['_id'] = (string) $userArray['_id'];
        $userArray['avatar'] = $userArray['avatar'] ?? Cdn::avatar('default.jpg');
        if ($user) {
            $userArray['is_followed'] = $this->is_followed ?? false;
            $userArray['is_online'] = $userArray['is_followed'] ? $this->is_online_for_followers : $this->is_online;
            $userArray['is_liked'] = $this->is_liked ?? false;
        }

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $userArray[$field] ?? null;
        }

        return $output;
    }
}
