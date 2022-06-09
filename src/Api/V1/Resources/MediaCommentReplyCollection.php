<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\MediaComment;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @mixin MediaComment
 */
class MediaCommentReplyCollection extends ResourceCollection
{
    public $collects = MediaCommentResource::class;
}
