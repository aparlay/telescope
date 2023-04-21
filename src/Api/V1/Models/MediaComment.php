<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Models\MediaComment as MediaCommentBase;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * Class MediaLike.
 */
class MediaComment extends MediaCommentBase
{
    /**
     * Get the media associated with the alert.
     */
    public function mediaObj(): \Illuminate\Database\Eloquent\Relations\BelongsTo|BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
