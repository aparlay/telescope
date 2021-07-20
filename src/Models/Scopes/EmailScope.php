<?php


namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\Email;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

trait EmailScope
{
    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeUser($query, $userId): mixed
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);
        return $query->where('user_id', $userId);
    }

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

    /**
     * @param  Builder  $query
     * @param  UTCDateTime  $start
     * @param  UTCDateTime  $end
     * @return Builder
     */
    public function scopeDate(Builder $query, UTCDateTime $start, UTCDateTime $end): Builder
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
