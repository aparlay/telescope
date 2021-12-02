<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Media;
use Illuminate\Database\Eloquent\Builder;

trait EmailScope
{
    use BaseScope;

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOpened(Builder $query): Builder
    {
        return $query->where('status', Email::STATUS_OPENED);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', Email::STATUS_SENT);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', Email::STATUS_FAILED);
    }
}
