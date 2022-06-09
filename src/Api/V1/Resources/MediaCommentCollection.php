<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\MediaComment;

/**
 * @mixin MediaComment
 */
class MediaCommentCollection extends AbstractResourceCollection
{
    public $collects = MediaCommentResource::class;
}
