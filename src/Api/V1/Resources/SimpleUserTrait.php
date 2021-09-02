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
        $userArray['is_followed'] = false;
        $userArray['is_liked'] = false;
        if ($user) {
            $userArray['is_followed'] = isset($this->creator['_id'], $user->following[(string) $this->creator['_id']]);
            $userArray['is_liked'] = (string) $this->creator['_id'] === (string) $user->_id;
        }

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $userArray[$field] ?? null;
        }

        return $output;
    }
}
