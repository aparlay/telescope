<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Helpers\Cdn;
use Exception;

trait SimpleMediaTrait
{
    /**
     * Create the simple user attribute.
     *
     * @param string[] $fields
     *
     * @throws Exception
     */
    public function createSimpleMedia(array $mediaArray, array $fields = ['_id', 'cover', 'file', 'status']): array
    {
        $mediaArray['_id']    = (string) $mediaArray['_id'];
        $mediaArray['cover']  = $mediaArray['cover'] ?? Cdn::cover('default.jpg');
        $mediaArray['file']   = $mediaArray['file']  ?? Cdn::video('default.jpg');
        $mediaArray['status'] = (int) $mediaArray['status'];

        $output               = [];
        foreach ($fields as $field) {
            $output[$field] = $mediaArray[$field] ?? null;
        }

        return $output;
    }
}
