<?php

namespace Aparlay\Core\Admin\Resources;

trait SimpleUserTrait
{
    /**
     * Create the simple user attribute.
     *
     * @param string[] $fields
     *
     */
    public function createSimpleUser(
        $user,
        array $fields = ['_id', 'username', 'email', 'full_name', 'status', 'status_badge', 'visibility', 'follower_count', 'like_count', 'media_count', 'created_at']
    ): array {
        $user['_id'] = (string) $user['_id'];

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $user[$field] ?? null;
        }

        return $output;
    }
}
