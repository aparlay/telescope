<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
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
        array $fields = ['_id', 'username', 'avatar', 'is_followed', 'is_liked'],
        User|Media|null $subject = null
    ): array {
        $user = auth()->user();
        $userArray['_id'] = (string) $userArray['_id'];
        $userArray['avatar'] = $userArray['avatar'] ?? Cdn::avatar('default.jpg');
        if ($user && $subject !== null) {
            $userArray['is_followed'] = $subject instanceof User ? $subject->is_followed : false;
            $userArray['is_liked'] = $subject instanceof Media ? $subject->is_liked : false;
        }

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $userArray[$field] ?? null;
        }

        return $output;
    }
}
