<?php

namespace Aparlay\Core\Admin\Resources;

trait SimpleMediaTrait
{
    /**
     * Create the simple user attribute.
     *
     * @param string[] $fields
     */
    public function createSimpleMedia(
        $media,
        array $fields = ['_id', 'file', 'creator.username', 'description', 'status', 'like_count', 'visit_count', 'sort_score', 'like_count', 'media_count', 'created_at']
    ): array {
        $media['_id'] = (string) $media['_id'];

        $output = [];
        foreach ($fields as $field) {
            $output[$field] = $user[$field] ?? null;
        }

        return $output;
    }
}
