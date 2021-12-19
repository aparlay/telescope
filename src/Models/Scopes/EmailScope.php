<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Enums\EmailStatus;
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
        return $query->where('status', EmailStatus::OPENED->value);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', EmailStatus::SENT->value);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', EmailStatus::FAILED->value);
    }
}
