<?php

namespace Aparlay\Core\Models\Traits;

use Aparlay\Core\Models\BaseModel;
use Aparlay\Core\Models\User;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * @mixin BaseModel
 */
trait Creator
{
    /**
     * Get the creator associated with the follow.
     */
    public function creatorObj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator._id');
    }

}
