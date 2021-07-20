<?php

namespace Aparlay\Core\Models\Scopes;

use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

trait UserScope
{
    /**
     * @param Builder $query
     * @param string $email
     * @return mixed
     */
    public function scopeEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * @param Builder $query
     * @param string $phone
     * @return mixed
     */
    public function scopePhoneNumber(Builder $query, string $phone): Builder
    {
        return $query->where('phone_number', $phone);
    }

    /**
     * @param Builder $query
     * @param string $username
     * @return mixed
     */
    public function scopeUsername(Builder $query, string $username): Builder
    {
        return $query->where('username', $username);
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('type', User::TYPE_ADMIN);
    }

    /**
     * @param  Builder  $query
     * @param  UTCDateTime  $since
     * @return mixed
     */
    public function scopePendingSince(Builder $query, UTCDateTime $since): Builder
    {
        return $query->where('status', User::STATUS_PENDING)
            ->where('hide_moderation_till', '$gte', $since);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeEnable(Builder $query): Builder
    {
        return $query->where('status', '$in', [
            User::STATUS_PENDING,
            User::STATUS_VERIFIED,
            User::STATUS_ACTIVE,
        ]);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDisable(Builder $query): Builder
    {
        return $query->where('status', '$in', [
            User::STATUS_SUSPENDED,
            User::STATUS_BLOCKED,
            User::STATUS_DEACTIVATED,
        ]);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', User::VISIBILITY_PRIVATE);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', User::VISIBILITY_PUBLIC);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', User::STATUS_PENDING);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', User::STATUS_ACTIVE);
    }

    /**
     * @param  Builder  $query
     * @param  ObjectId|string  $userId
     * @return mixed
     */
    public function scopeUser(Builder $query, ObjectId | string $userId): Builder
    {
        $userId = $userId instanceof ObjectId ? $userId : new ObjectId($userId);

        return $query->where('user_id', $userId);
    }

    /**
     * @param  Builder  $query
     * @param  UTCDateTime|null  $start
     * @param  UTCDateTime|null  $end
     * @return Builder
     */
    public function scopeDate(Builder $query, UTCDateTime $start = null, UTCDateTime $end = null): Builder
    {
        if ($start !== null && $end !== null) {
            return $query->whereBetween('created_at', [$start, $end]);
        }

        if ($start !== null) {
            return $query->where('created_at', '$gte', $start);
        }

        if ($end !== null) {
            return $query->where('created_at', '$lte', $end);
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecentFirst($query): mixed
    {
        return $query->orderBy('created_at', 'desc');
    }
}
